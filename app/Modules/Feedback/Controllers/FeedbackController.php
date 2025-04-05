<?php

namespace App\Modules\Feedback\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Feedback\Models\Feedback;
use App\Modules\Feedback\Requests\FeedbackRequest;
use App\Modules\Feedback\Resources\FeedbackResource;
use App\Modules\Feedback\Services\FeedbackService;
use Illuminate\Http\Request;
use Throwable;

class FeedbackController extends Controller
{
    private $feedbackService;
    public function __construct()
    {
        $this->feedbackService = new FeedbackService();
    }
    public function listAllFeedbacks(Request $request)
    {
        try {
            $feedback =  $this->feedbackService->listAllFeedbacks($request);
            return (FeedbackResource::collection($feedback))
            ->response()->setStatusCode(200);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant list a Patient',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function newFeedback(FeedbackRequest $request) {
        try {
            $this->feedbackService->newFeedback($request->all());
            return response()->json([
                'message' => 'Feedback created successfully'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant Create a Feedback',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function editFeedback(FeedbackRequest $request, Feedback $feedback) {
            try {
                $this->feedbackService->editFeedback($request->all(), $feedback);
                return response()->json([
                    'message' => 'Feedback updated successfully'
                ]);
            } catch (Throwable $e) {
                return response()->json([
                    'message' => 'Cant update a Feedback',
                    'error' => $e->getMessage()
                ]);
            }
    }

    public function deletedFeedback(Feedback $feedback) {
        try {
            $this->feedbackService->deletedFeedback($feedback);
            return response()->json([
                'message' => 'Feedback deleted successfully'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Cant delete a Feedback',
                'error' => $e->getMessage()
            ]);
        }
    }
}
