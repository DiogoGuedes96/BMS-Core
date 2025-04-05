<?php

namespace App\Modules\Companies\Services;

use App\Services\Service;
use App\Modules\Companies\Models\Company;
use Illuminate\Support\Facades\DB;
use App\Services\FileService;

class CompanyService extends Service
{
    public function __construct(private FileService $fileService)
    {
    }

    public function getCompany($id = null){
        if ($id) {
            return Company::find($id);
        }

        return Company::first();
    }

    public function create($request){
        DB::beginTransaction();

        try {
            if (!isset($request['automatic_notification'])) {
                $request['automatic_notification'] = true;
            }

            if (!$company = Company::create($request)) {
                throw new \Exception('Houve um erro ao tentar criar a empresa.');
            }

            if (isset($request['image'])) {
                $partialPath = 'companies/' . $company->id;

                if (!$filePath = $this->fileService->storeFile($request['image'], $partialPath)) {
                    throw new \Exception('Houve um erro ao tentar criar a empresa.');
                }

                $company->update(['img_url' => $filePath]);
            }

            DB::commit();

            return $this->result($company, true);
        } catch(\Exception $error) {
            DB::rollback();
            return $this->result($error->getMessage(), false);
        }
    }

    public function update($request, $company = null)
    {
        DB::beginTransaction();

        try {
            if (empty($company)) {
                $company = $this->getCompany();
            }

            $partialPath = 'companies/' . $company->id;

            if(!isset($request['img_path']) && isset($request['image'])) {
                $this->fileService->removeEverythingFromPath($partialPath);
                $filePath = $this->fileService->storeFile($request['image'], $partialPath);
                $request['img_url'] = $filePath;
            }

            if (!$company->update($request)) {
                throw new \Exception('Houve um erro ao tentar atualizar o registro.');
            }

            DB::commit();

            return $this->result($company, true);
        } catch(\Exception $error) {
            DB::rollback();

            return $this->result($error->getMessage(), false);
        }
    }
}
