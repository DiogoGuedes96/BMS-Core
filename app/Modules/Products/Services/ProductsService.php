<?php

namespace App\Modules\Products\Services;

use App\Services\Service;
use App\Modules\Products\Models\Products;

class ProductsService extends Service
{
    private $bmsProduct;

    public function __construct()
    {
        $this->bmsProduct = new Products();
    }

    public function list($query)
    {
        $model = $this->bmsProduct;

        if (!empty($query['search'])) {
            $model = $model->where('name', 'like', "%{$query['search']}%")
                ->orWhere('email', 'like', "%{$query['search']}%");
        }

        if (!empty($query['sort']) && !empty($query['order'])) {
            $model = $model->orderBy($query['sort'], $query['order']);
        }

        return $model->paginate($query['per_page'] ?? 10);
    }

    public function listAll($request)
    {
        $model = $this->bmsProduct;

        if (!empty($request['status']) && $request['status'] !== 'all') {
            $model = $model->where('status', $request['status'] === 'active' ? 1 : 0);
        }

        return $model->get();
    }

    public function show(int $id)
    {
        return $this->bmsProduct->find($id);
    }

    public function store($data)
    {
        return $this->bmsProduct->create($data);
    }

    public function edit(int $id, $data)
    {
        return $this->bmsProduct->find($id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->bmsProduct->find($id)->delete();
    }
}
