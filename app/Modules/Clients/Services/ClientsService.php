<?php

namespace App\Modules\Clients\Services;

use App\Http\Resources\PaginationResource;
use Exception;
use App\Modules\Clients\Models\Clients;
use App\Modules\Clients\Models\ClientResponsible;
use App\Modules\Clients\Resources\ClientResource;

class ClientsService
{
    protected $client;
    protected $clientResponsibleService;

    public function __construct()
    {
        $this->client                   = new Clients();
        $this->clientResponsibleService = new ClientResponsibleService();
    }

    public function getAllClients($perPage = null)
    {
        try {
            $clients = Clients::with(['clientResponsibles'])->orderBy('created_at', 'desc');
            $clients = $perPage ? $clients->paginate($perPage) : $clients->get();

            return $clients;
        } catch (Exception $e) {
            throw new Exception('error', $e->getCode());
        }
    }

    public function getFilteredClients($search, $status, $type, $sorter, $perPage)
    {
        try {
            $clients = Clients::with(['clientResponsibles'])
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                })
                ->when($sorter === 'ascend', fn ($query) => $query->orderBy('name', 'asc'))
                ->when($sorter === 'descend', fn ($query) => $query->orderBy('name', 'desc'));
                if($type) {
                    $clients = $clients->where('type', $type);
                }
                
                if($status === 1 || $status === 0) {
                    $clients = $clients->where('status', $status);
                };

                $clients = $perPage ? $clients->paginate($perPage ?? 10) : $clients->get();
    
            return $clients;
        } catch (Exception $e) {
            throw new Exception('error', $e->getCode());
        }
    }

    public function createClient(array $request)
    {
        try {
            $responsibles = $request['responsibles'] ?? null;

            $responsiblesToAttach = [];
            if ($request['responsibles']) {
                foreach ($responsibles as $responsible) {

                    $newClientResponsible = $this->clientResponsibleService->createOrUpdateClientResponsible($responsible);
                    array_push($responsiblesToAttach, $newClientResponsible->id);
                }
            }

            $newClient = $this->storeClient(
                $request["name"],
                $request["nif"] ?? null,
                $request["type"],
                $request["email"] ?? null,
                $request["address"] ?? null,
                $request["phone"],
                $request["status"] ?? null
            );

            if (empty($newClient)) {
                throw new Exception("Something went wrong wile creating a new client!", 500);
            }

            if (!empty($responsiblesToAttach)) {
                $newClient->clientResponsibles()->attach($responsiblesToAttach);
            }
        } catch (Exception $e) {
            throw new Exception('error', $e->getCode());
        }
    }

    public function storeClient($name, $nif, $type, $email, $address, $phone, $status)
    {
        try {
            return $this->client->create(
                [
                    "name"    => $name,
                    "nif"     => $nif,
                    "type"    => $type,
                    "email"   => $email,
                    "address" => $address,
                    "phone"   => $phone,
                    "status"  => $status,
                ]
            );
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 500);
        }
    }

    public function editClient(array $request)
    {
        try {
            if (isset($request['id'])) {
                $client = $this->client->find($request['id']);

                $updateData = [];

                if (!empty($request['name'])) {
                    $updateData['name'] = $request['name'];
                }

                if (!empty($request['email'])) {
                    $updateData['email'] = $request['email'];
                }

                if (!empty($request['type'])) {
                    $updateData['type'] = $request['type'];
                }

                if (!empty($request['address'])) {
                    $updateData['address'] = $request['address'];
                }

                if (!empty($request['nif'])) {
                    $updateData['nif'] = $request['nif'];
                }

                if (!empty($request['phone'])) {
                    $updateData['phone'] = $request['phone'];
                }

                if (isset($request['status'])) {
                    $updateData['status'] = $request['status'];
                }

                if (empty($updateData)) {
                    throw new exception('No data was given!', 422);
                }

                $client->update($updateData);

                $clientResponsibles = $request['responsibles'] ?? null;

                if ($clientResponsibles) {
                    $client->clientResponsibles()->detach();
                    $responsiblesToAttach = [];
                    foreach ($clientResponsibles as $clientResponsible) {
                        $updatedResponsible = $this->clientResponsibleService->createOrUpdateClientResponsible($clientResponsible);
                        array_push($responsiblesToAttach, $updatedResponsible->id);
                    }

                    $client->clientResponsibles()->attach($responsiblesToAttach);
                }
            }
        } catch (Exception $e) {
            throw new Exception('error', $e->getCode());
        }
    }

    public function softDeleteClient($clientId)
    {
        try {
            $client = $this->client->find($clientId);

            if (!$client) {
                throw new Exception("Client not found!", 404);
            }

            $client->clientResponsibles()->detach();
            $client->delete();
        } catch (Exception $e) {
            throw new Exception('error', $e->getCode());
        }
    }


    //TODO DIOGO RAPHA, change later
    public function getClientByPhoneNumber($number, $name = null){
        try {
            $result = null;
        
            $query = $this->client->where('phone', $number);
    
            if ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            }
    
            $client = $query->first();
    
            if ($client) {
                $result = [
                    'entity' => 'client',
                    'name' => $client->name,
                    'email' => $client->email,
                    'type' => $client->type,
                    'address' => $client->address,
                    'nif' => $client->nif,
                    'phone' => $client->phone,
                    'status' => $client->status,
                ];
    
            } else {
                $responsible = $this->clientResponsibleService->getClientResponsibleByPhoneNumber($number, $name);
                if ($responsible) {
                    $result = [
                        'entity'  => 'responsible',
                        'name'    => $responsible->name,
                        'phone'   => $responsible->phone,
                        'email'   => null,
                        'type'    => null,
                        'address' => null,
                        'nif'     => null,
                        'status'  => null,
                    ];
                }
            }
    
            return $result;
        } catch (Exception $e) {
            throw new Exception('error', $e->getCode());
        }   
    }
    
    public function getTotal(string $type = 'all'): int
    {
        return Clients::count();
    }
}
