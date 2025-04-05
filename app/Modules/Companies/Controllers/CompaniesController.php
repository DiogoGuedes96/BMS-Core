<?php

namespace App\Modules\Companies\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Companies\Requests\CreateOrUpdateCompanyRequest;
use App\Modules\Companies\Resources\CompanyResource;
use App\Modules\Companies\Services\CompanyService;

class CompaniesController extends Controller
{
    public function __construct(
        private CompanyService $companyService,
    ){

    }

    public function show()
    {
        if (!$company = $this->companyService->getCompany()) {
            return response()->json([
                'data' => null,
            ], 200);
        }

        return (new CompanyResource($company))
    		->response()->setStatusCode(200);
    }

    public function store(CreateOrUpdateCompanyRequest $request)
    {
        $result = $this->companyService->create($request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content,
            ], 400);
        }

        return (new CompanyResource($result->content))
    		->response()->setStatusCode(201);
    }

    public function update(CreateOrUpdateCompanyRequest $request)
    {
        $result = $this->companyService->update($request->all());

        if (!$result->success) {
            return response()->json([
                'message' => $result->content
            ], 400);
        }

        return (new CompanyResource($result->content))
    		->response()->setStatusCode(200);
    }
}
