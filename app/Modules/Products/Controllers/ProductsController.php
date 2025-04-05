<?php

namespace App\Modules\Products\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Products\Requests\ProductsRequest;
use App\Modules\Products\Resources\ProductsResources;
use Illuminate\Http\Request;
use App\Modules\Products\Services\ProductsService;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductsController extends Controller
{
    /**
     * @var BmsProductsService
     */
    private $bmsProductsService;

    public function __construct()
    {
        $this->bmsProductsService = new ProductsService();
    }

    public function index()
    {
        return view('products::index');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list(Request $request)
    {
        try {
            $products = $this->bmsProductsService->list($request->query());

            if (!$products) {
                return response()->json(['message' => 'error', 'error' => 'No products found!'], 404);
            }
            return  $products;
        } catch (Throwable $th) {
            Log::error($th);
            return response()->json(['message' => 'error', 'error' => 'Try again later!', 500]);
        }
    }

    public function listAll(Request $request)
    {
        try {
            $products = $this->bmsProductsService->listAll($request);

            if (!$products) {
                return response()->json(['message' => 'error', 'error' => 'No products found!'], 404);
            }
            return  $products;
        } catch (Throwable $th) {
            Log::error($th);
            return response()->json(['message' => 'error', 'error' => 'Try again later!', 500]);
        }
    }

    public function show($id)
    {
        try {
            $product = $this->bmsProductsService->show($id);

            return (new ProductsResources($product))
                ->response()->setStatusCode(200);
        } catch (Throwable $th) {
            Log::error($th);
            return response()->json([
                'message' => 'Erro ao buscar produto',
            ], 404);
        }
    }

    public function store(ProductsRequest $request)
    {
        try {
            $product = $this->bmsProductsService->store($request->all());

            return (new ProductsResources($product))
                ->response()->setStatusCode(201);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao salvar novo produto',
            ], 404);
        }
    }

    public function edit($id, ProductsRequest $request)
    {
        try {
            $product = $this->bmsProductsService->edit($id, $request->all());

            return response()->json([
                'message' => 'Produto editado com sucesso',
            ], 200);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao editar produto',
            ], 404);
        }
    }

    public function delete($id)
    {
        try {
            $this->bmsProductsService->delete($id);

            return response()->json([
                'message' => 'Produto deletado com sucesso',
            ], 200);
        } catch (\Error $error) {
            return response()->json([
                'message' => 'Erro ao deletar produto',
            ], 404);
        }
    }
}
