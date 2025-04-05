<?php

namespace App\Modules\Business\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Business\Requests\BusinessRequest;
use App\Modules\Business\Resources\BusinessResource;
use App\Modules\Business\Services\BusinessService;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    protected $businessService;

    public function __construct()
    {
        $this->businessService = new BusinessService();
    }

    public function list(Request $request)
    {
        $user = $request->user();

        $businesses = $this->businessService->listBusinesses($request, $user);

        return response()->json([
            'data' => $businesses,
            'message' => 'List all businesses',
        ]);
    }

    public function store(BusinessRequest $request)
    {
        try {
            $result = $this->businessService->create($request->all());

            if (!$result->success) {
                return response()->json([
                    'message' => $result->content,
                ], 400);
            }

            return (new BusinessResource($result->content))
                ->response()->setStatusCode(201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error', 'error' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $result = $this->businessService->updateOneBusiness($id, $data);

        return response()->json([
            'message' => "Update business with id: $id",
        ]);
    }

    public function updateBusinessClient($id, $clientId)
    {
        try {
            $this->businessService->updateOneBusiness($id, ["client_id" => $clientId]);

            return response()->json([
                'message' => "Update business with id: $id",
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'error' => $th->getMessage()], 500);
        }
    }

    public function updateState(Request $request, $id)
    {
        $data = $request->all();

        $result = $this->businessService->updateState($id, $data);

        return response()->json([
            'message' => "Update business with id: $id",
        ]);
    }

    public function delete(Request $request, $id)
    {
        return response()->json([
            'message' => "Delete business with id: $id",
        ]);
    }

    public function show($id)
    {
        try {
            $result = $this->businessService->findById($id);

            return (new BusinessResource($result->content))
                ->response()->setStatusCode(201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function cancelOneBusiness($id)
    {
        $result = $this->businessService->cancelOneBusiness($id);

        return response()->json([
            'message' => "Cancel business with id: $id",
        ]);
    }

    public function closeOneBusiness($id)
    {
        $result = $this->businessService->closeOneBusiness($id);

        return response()->json([
            'message' => "Cancel business with id: $id",
        ]);
    }

    public function reopenOneBusiness($id)
    {
        $result = $this->businessService->reopenOneBusiness($id);

        return response()->json([
            'message' => "Cancel business with id: $id",
        ]);
    }
}
