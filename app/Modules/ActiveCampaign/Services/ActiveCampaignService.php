<?php

namespace App\Modules\ActiveCampaign\Services;

use App\Modules\Business\Enums\KanbanTypesEnum;
use App\Modules\Business\Models\Business;
use App\Modules\Business\Models\BusinessFollowup;
use App\Modules\Business\Models\BusinessKanban;
use App\Modules\Business\Models\BusinessNotes;
use App\Modules\UniClients\Models\UniClients;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ActiveCampaignService
{
    const DEAL_CLOSER = 'DEAL_CLOSER';
    const DEAL_REFERENCIADOR = 'DEAL_REFERENCIADOR';
    const DEAL_BUSINESS_COACH = 'DEAL_BUSINESS_COACH';

    public function __construct()
    {
    }

    function request(String $method, String $url, array $data = [], String $apiVersion = "/api/3/")
    {
        $client = new Client(['verify' => false, 'timeout' => 180]);
        $retryCount = 0;

        try {
            $data = array_merge(
                [
                    'headers' => [
                        'Api-Token' => env("AC_TOKEN"),
                    ]
                ],
                ['json' => $data],
            );

            $uri = env("AC_URL") . $apiVersion . $url;

            $response = $client->request($method, $uri, $data);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return json_decode($response->getBody()->getContents());
            }
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            if ($e->getCode() === 0) {
                throw $e;
            }
        } catch (\Exception $e) {
            if ($e->getCode() === 401) {
                $this->request($method, $url, $data);
                $retryCount++;
            } else {
                throw $e;
            }
        }

        return;
    }

    public function searchContact(String $email)
    {
        if (empty($email)) {
            throw new Exception("Email params is empty or invalid", 1);
        }

        $url = "contacts?status=-1&search=" . trim($email);

        $request = $this->request('get', $url);

        if (empty($request->contacts)) {
            return null;
        }

        $contact = collect($request->contacts)->map(function ($con) {

            $firstName = property_exists($con, 'firstName') ? $con->firstName : '';
            $lastName = property_exists($con, 'lastName') ? $con->lastName : '';
            $phone = property_exists($con, 'phone') ? preg_replace('/\D/', '', $con->phone) : null;

            $name = $firstName . " " . $lastName;

            return [
                "id" => $con->id,
                "email" => $con->email,
                "name" => $name,
                "phone" => $phone
            ];
        });

        return $contact;
    }

    public function createDeal(Business $business, UniClients $client)
    {
        try {
            $owner = $this->request('get', "users/email/" . env("AC_EMAIL"));
            $group = $this->findGroup($business);
            $contact = $this->findOrCreateContact($client);

            $contactId = null;

            if (is_array($contact) && !empty($contact["id"])) {
                $contactId = $contact["id"];
            } elseif (is_object($contact) && !empty($contact->contact)) {
                $contactId = $contact->contact->id;
            }

            if (empty($group)) {
                return throw new Exception("Grupo do AC nao encontrado", 404);
            }

            $stage = $business->stage()->first();
            $acStage = $this->findStage($group->id, $stage);

            $dataDeal = [
                //require properties
                "title" => $business->name,
                "value" => ((float)$business->value * 100),
                "currency" => $group->currency,
                "group" => $group->id,
                "owner" => $owner->user->id,
                //optional properties
                "contact" => $contactId,
                "description" => $business->description,
                "status" => $this->getStatus($business)
            ];

            if (!empty($acStage)) {
                $dataDeal["stage"] = $acStage->id;
            }

            $deal = $this->request('post', "deals", ["deal" => $dataDeal]);
            return $deal->deal->id;
        } catch (\Exception $e) {
            return;
        }
    }

    public function updateDeal(Business $business)
    {
        try {
            if (!$business->acId) {
                return;
            }

            $business = $business
                ->with('client')
                ->with('stage')
                ->find($business->id);

            $stage = $business->stage()->first();

            $deal = $this->request('get', "deals/" . $business->acId);

            if (!empty($deal)) {
                $deal = $deal->deal;
            }

            $changeStage = $this->findStage($deal->group, $stage);
            $contact = $this->findOrCreateContact($business->client);

            $contactId = null;

            if (is_array($contact) && !empty($contact["id"])) {
                $contactId = $contact["id"];
            } elseif (is_object($contact) && !empty($contact->contact)) {
                $contactId = $contact->contact->id;
            }

            $customFields = $this->setDealResponsibles($business);

            $dataDeal = [
                //require properties
                "title" => $business->name,
                "value" => ((float)$business->value * 100),
                "currency" => $deal->currency,
                "stage" => $deal->stage,
                "group" => $deal->group,
                "owner" => $deal->owner,
                //optional properties
                "contact" => $contactId,
                "description" => $business->description,
                "status" => $this->getStatus($business),
            ];

            if (!empty($changeStage) && $changeStage->id != $deal->id) {
                $dataDeal["stage"] = $changeStage->id;
            }

            if (!empty($customFields)) {
                $dataDeal["fields"] = $customFields;
            }

            $deal = $this->request('put', "deals/" . $business->acId, ["deal" => $dataDeal]);

            return $deal->deal->id;
        } catch (\Exception $e) {
            return;
        }
    }

    public function moveStage($business)
    {
        try {
            $business = $business
                ->with('client')
                ->with('stage')
                ->find($business->id);

            $stage = $business->stage()->first();

            $deal = $this->request('get', "deals/" . $business->acId);

            if (!empty($deal)) {
                $deal = $deal->deal;
            }

            $changeStage = $this->findStage($deal->group, $stage);

            $dataDeal = [
                "status" => $this->getStatus($business)
            ];

            if (!empty($changeStage) && $changeStage->id != $deal->id) {
                $dataDeal["stage"] = $changeStage->id;
            }

            $deal = $this->request('put', "deals/" . $business->acId, ["deal" => $dataDeal]);
        } catch (\Exception $e) {
            return;
        }
    }

    private function findOrCreateContact(UniClients $client)
    {

        $contact = $this->searchContact($client->email);

        if (!empty($contact)) return $contact[0];

        $url = "contacts?status=-1&search=" . trim($client->email);

        $name = explode(" ", $client->name) ?? [];

        $newContact = $this->request('post', $url, [
            "contact" => [
                "email" => $client->email,
                "firstName" => count($name) >= 1 ? $name[0] : "",
                "lastName" => count($name) > 1 ? $name[1] : "",
                "phone" => $client->phone,
            ]
        ]);

        return $newContact;
    }

    private function findGroup($business)
    {
        $stages = $this->request('get', 'dealStages');

        $listGroupId = array_unique(collect($stages->dealStages)->map(function ($stage) {
            return $stage->id;
        })->toArray());

        /** @var BusinessKanban */
        $kanbantype = $business->businessKanban;

        $dealGroup = null;

        foreach ($listGroupId as $groupId) {
            $group = $this->request('get', "dealStages/$groupId/group");
            if (KanbanTypesEnum::getTypeValue($group->dealGroup->title) === $kanbantype->type) {
                $dealGroup = $group->dealGroup;
                break;
            }
        }
        return $dealGroup;
    }

    private function findStage($groupId, $kanbanStage)
    {
        $stages = $this->request('get', "dealStages?filters[d_groupid]=$groupId");

        foreach ($stages->dealStages as $stage) {
            if ($stage->title === $kanbanStage->name) {
                return $stage;
            }
        }
    }

    private function getStatus($business)
    {
        switch ($business->closed_state) {
            case 'ganho':
                return 1;
            case 'perdido':
                return 2;
            default:
                return 0;
        }
    }

    private function getDealCustomFields($customs = [self::DEAL_REFERENCIADOR, self::DEAL_BUSINESS_COACH, self::DEAL_CLOSER])
    {
        $customFields = $this->request('get', 'dealCustomFieldMeta');

        if (empty($customFields)) {
            return [];
        }

        if (empty($customs)) {
            return collect($customFields->dealCustomFieldMeta)->map(function ($field) {
                return [
                    'name' => $field->personalization,
                    'id' => $field->id
                ];
            });
        }

        return collect($customFields->dealCustomFieldMeta)->filter(function ($field) use ($customs) {
            return in_array($field->personalization, $customs);
        })->map(function ($field) {
            return [
                'name' => $field->personalization,
                'id' => $field->id
            ];
        });
    }

    private function getCustomFieldByName($customFields, $name)
    {
        return collect($customFields)->first(function ($field) use ($name) {
            return $field['name'] === $name;
        });
    }

    private function setDealResponsibles(Business $business)
    {
        $fields = [];

        $customFields = $this->getDealCustomFields();

        $fieldReferrer = $this->getCustomFieldByName($customFields, self::DEAL_REFERENCIADOR);
        $fieldCoach = $this->getCustomFieldByName($customFields, self::DEAL_BUSINESS_COACH);
        $fieldCloser = $this->getCustomFieldByName($customFields, self::DEAL_CLOSER);

        if ($business->referrer_id) {
            array_push($fields, ['customFieldId' => $fieldReferrer["id"], "fieldValue" => $business->referrer->name]);
        }

        if ($business->closer_id) {
            array_push($fields, ['customFieldId' => $fieldCloser["id"], "fieldValue" => $business->closer->name]);
        }

        if ($business->coach_id) {
            array_push($fields, ['customFieldId' => $fieldCoach["id"], "fieldValue" => $business->coach->name]);
        }

        return $fields;
    }

    public function createOrUpdateDealNote(BusinessNotes $businessNotes)
    {
        if (empty($businessNotes->business->acId)) {
            return;
        }

        $method = 'post';
        $url = 'deals/' . $businessNotes->business->acId . '/notes';

        if (!empty($businessNotes->acId)) {
            $method = 'put';
            $url = 'deals/' . $businessNotes->business->acId . '/notes/' . $businessNotes->acId;
        }

        $deal = $this->request($method, $url, [
            'note' => [
                "note" => $businessNotes->content
            ]
        ]);
        if (!empty($deal)) {
            return $deal->note;
        }

        return;
    }

    public function deleteDealNote(BusinessNotes $businessNotes)
    {
        if (empty($businessNotes->acId)) {
            return;
        }

        try {
            $this->request('delete', 'notes/' . $businessNotes->acId);
        } catch (\Exception $e) {
            return;
        }
    }

    public function deleteDealFollowup(BusinessFollowup $businessFollowup)
    {
        try {
            $this->request('delete', 'dealTasks/' . $businessFollowup->acId);
        } catch (\Exception $e) {
            return;
        }
    }

    public function createOrUpdateDealFollowup(BusinessFollowup $businessFollowup)
    {
        $method = 'post';
        $url = 'dealTasks';
        if (!empty($businessFollowup->acId)) {
            $method = 'put';
            $url = 'dealTasks/' . $businessFollowup->acId;
        }

        $dueDate = $businessFollowup->date . ' ' . $businessFollowup->time;

        $data = [
            'dealTask' => [
                "title" => $businessFollowup->title,
                "note" => $businessFollowup->title . ' Assigned:' . $businessFollowup->responsible->name,
                "ownerType" => "deal",
                "relid" => $businessFollowup->business->acId,
                "status" => $businessFollowup->completed,
                "duedate" => (new DateTime($dueDate))->format('Y-m-d\TH:i:sP'),
                "dealTasktype" => 1,
            ]
        ];

        try {
            $deal = $this->request($method, $url, $data);
            if (!empty($deal)) {
                return $deal->dealTask;
            }
        } catch (\Exception $e) {
            return;
        }

        return;
    }
}
