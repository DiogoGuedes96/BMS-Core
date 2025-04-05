<?php

namespace App\Modules\Feedback\Services;

use App\Modules\Feedback\Models\Feedback;
use App\Modules\Feedback\Models\FeedbackWho;
use Illuminate\Support\Collection as IlluminateCollection;

class FeedbackService
{
    private $feedback;
    private $feedbackWho;
    public function __construct()
    {
        $this->feedback = new Feedback();
        $this->feedbackWho = new FeedbackWho();
    }
    public function listAllFeedbacks($request)
    {
        $search = $request->get('search', '');
        $searchStartDate = $request->get('searchStartDate');
        $searchEndDate = $request->get('searchEndDate');

        $feedbacks = Feedback::with(['feedbackWho']);

        if ($search) {
            $feedbacks = $feedbacks->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('patient_number', 'like', '%' . $search . '%');
            });
        }

        if($searchStartDate || $searchEndDate) {
            $feedbacks = $feedbacks->where(function ($query) use ($searchStartDate, $searchEndDate) {
                if ($searchStartDate && $searchEndDate) {
                    $query->whereBetween('date', [$searchStartDate, $searchEndDate]);
                } elseif ($searchStartDate) {
                    $query->where('date', '>=', $searchStartDate);
                } elseif ($searchEndDate) {
                    $query->where('date', '<=', $searchEndDate);
                }
            });
        } else {
            if (!$request->sorter)
            $feedbacks->orderBy('date', 'asc');
        }

        $feedbacks = $feedbacks->when($request->sorter === 'ascend', function ($query) {
            return $query->orderBy('name', 'asc');
        })
        ->when($request->sorter === 'descend', function ($query) {
            return $query->orderBy('name', 'desc');
        })
        ->paginate($request->get('perPage') ?? 15);
        return $feedbacks;
    }

    public function newFeedback(array $feedbackData) {
        $newFeedback = $this->feedback->create($this->createFeedbackArray($feedbackData));
        foreach($feedbackData['feedbackWho'] as $who) {
            $who['feedback_id'] = $newFeedback->id;
            $this->feedbackWho->create($who);
        }
    }

    public function editFeedback(array $feedbackData, $feedback) {
        $feedback->update($this->createFeedbackArray($feedbackData));
        $whoArray = $this->feedbackWho->findWhoByFeedbackId($feedback->id)->get()->toArray();
        foreach ($feedbackData["feedbackWho"] as $feedbackW) {
            $findResponsible = collect(array_filter($whoArray, function ($value) use ($feedbackW) {
                return $value['id'] == $feedbackW["id"];
            }));
            $index = $this->findIndexIntoArrayAndCollection($whoArray, $findResponsible);
            if (!$findResponsible->isEmpty()) {
                $this->validateResponsibleEdit($feedbackW);
                array_splice($whoArray, $index, 1);
            } else {
                $feedbackW["feedback_id"] = $feedback->id;
                $this->feedbackWho->create($feedbackW);
            }
        }
        foreach($whoArray as $who){
            $this->feedbackWho->where("id", $who["id"])->delete();
        }
        return $feedback;
    }

    public function deletedFeedback($feedback) {
        return $this->feedback->where("id", $feedback->id)->delete();

    }

    private function createFeedbackArray(array $feedback) {
        return [
            'name' => $feedback['name'],
            'patient_number' => $feedback['patient_number'] ?? 0,
            'reason' => $feedback['reason'],
            'date' => $feedback['date'] ?? null,
            'time' => $feedback['time'] ?? null,
            'description' => $feedback['observations'],
        ];
    }

    private function validateResponsibleEdit(array $feedbackW)
    {
        $feedbackWho = FeedbackWho::where('id', $feedbackW["id"])->first();
        if ($feedbackWho->name !== $feedbackW["name"]) {
            $feedbackWho->name = $feedbackW["name"];
            $id = $feedbackWho->save();
        }
        else
        {
            $id = $feedbackWho->id;
        }

        return $id;
    }
    private function findIndexIntoArrayAndCollection(array $principal, IlluminateCollection $second)
    {

        foreach ($principal as $key => $value) {
            if (isset($second[$key])) {
                return $value["id"] === $second[$key]["id"] ? $key : null;
            }
        }
    }
}
