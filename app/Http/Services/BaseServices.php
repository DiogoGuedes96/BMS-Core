<?php

namespace App\Http\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class BaseServices
{
    public function organizeDynamicData(array $data, string $firstIndex, string $secondIndex): Collection
    {
        $dataCollection = collect([]);

        $dynamicData = array_filter($data, function ($value, $key) {
            return preg_match("/^dynamic_phone_number_/", $key);
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($dynamicData as $key => $value) {
            $i = substr($key, strrpos($key, '_') + 1);
            $dataCollection->push([
                "id" => isset($data["dynamic_id_" . $i]) ? $data["dynamic_id_" . $i] : "",
                $firstIndex => $data["dynamic_name_" . $i],
                $secondIndex => $data["dynamic_phone_number_" . $i]
            ]);
        }

        return $dataCollection;
    }

    public function storage(string $url, array $data)
    {
        try {

            $imagePath = null;
            if (!empty($data['file'])){
                $imagePath = array();
                for ($i = 0; $i < count($data["file"]); $i++) {
                    $file=$data["file"][$i];
                    array_push($imagePath, Storage::putFileAs($url, new File($file[0]), $file[1]));
                }
            }
            return $imagePath ? $imagePath : false;
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }
}
