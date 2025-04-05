<?php

namespace App\Modules\Primavera\Services;

use Exception;
use App\Modules\Primavera\Models\PrimaveraProductsBatch;

class PrimaveraProductsBatchService
{

    private $primaveraAuth;
    private $primaveraProductBatch;


    public function __construct()
    {
        $this->primaveraAuth               = new PrimaveraAuthService();
        $this->primaveraProductBatch       = new PrimaveraProductsBatch();
    }

    public function getAllProductBatches()
    {
        return $this->primaveraAuth->requestPrimaveraApi(
            'GET',
            '/WebApi/ApiExtended/LstProdutosStock'
        );
    }

   /**
    * It updates the products table with the data from the primavera database.
    *
    * @param command The command that is being run.
    */
    public function updateProductBatch($command)
    {
        try {
        $products = $this->getAllProductBatches();

        $primaveraProductsService = new PrimaveraProductsService();
        foreach ($products as $product) {
            $productId = $primaveraProductsService->getProductIdByPrimaveraId($product->ARTIGO);

            if(!$productId) {
                $command->error('Product with given id not found Id not found'. $product->ARTIGO);

                continue;
            }

            $productBatches = $product->LOTES;
                foreach ($productBatches as $batch) {
                    if(!$batch->LOTE) continue;

                    PrimaveraProductsBatch::updateOrCreate(
                        ['batch_number' => $batch->LOTE],
                        [
                            'active'               => $batch->ACTIVO,
                            'description'          => $batch->DESCRICAOLOTE,
                            'quantity'             => $batch->Quantidade,
                            'expiration_date'      => $batch->VALIDADE,
                            'primavera_product_id' => $productId,
                        ]
                    );
                    $command->info('Batch: ' . $batch->LOTE . ' Saved for product: ' . $product->ARTIGO);
                }
            }
        } catch (Exception $e) {
            $command->error('Error Saving Product Batchs');
            $command->error($e);
        }
    }
}
