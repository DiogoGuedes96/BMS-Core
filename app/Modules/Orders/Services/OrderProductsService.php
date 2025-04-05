<?php

namespace App\Modules\Orders\Services;

use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderProducts;
use App\Modules\Products\Models\Products;
use Illuminate\Http\Request;
use Exception;
use Modules\Products\Services\BmsProductsService;

class OrderProductsService
{
    private $bmsProductsService;
    private $bmsProducts;
    private $orderProduct;
    private $order;

    public function __construct()
    {
        $this->bmsProductsService = new BmsProductsService();
        $this->bmsProducts        = new Products();
        $this->orderProduct       = new OrderProducts();
        $this->order       = new Order();
    }

    /**
     * It saves the order products in the database.
     *
     * @param invoiceProduct is the product that is in the invoice
     * @param order the order that the product belongs to
     * @param command The command that was executed.
     */
    public function saveOrderProductFromInvoice($invoiceProduct, $order, $command)
    {
        try {
            $bmsProduct = $this->bmsProductsService->getBmsProductByPrimaveraId($invoiceProduct->Artigo);
            $orderProduct = OrderProducts::updateOrCreate(
                [
                    'name'                     => $invoiceProduct->Descricao ?? "", //Descricao is the name of the product in the primavera erp
                    'quantity'                 => $invoiceProduct->Quantidade ?? "",
                    'unit'                     => $invoiceProduct->Unidade ?? "",
                    'unit_price'               => $invoiceProduct->PrecoUnit ?? "",
                    'total_liquid_price'       => $invoiceProduct->TotalLiquido ?? "",
                    'discount_value'           => $invoiceProduct->Desconto ?? "",
                    'order_id'                 => $order->id,
                    'correction_price_percent' => 0.0,
                    'discount_percent'         => 0.0,
                    'bms_product'              => $bmsProduct->id,
                ]
            );
            $command->warn('OrderProduct ' . $orderProduct->id . ' created for order ' . $order->id);
        } catch (\Throwable $th) {
            $command->error('Error to save orderProduct ' . $invoiceProduct->Artigo . ' ' . $invoiceProduct->Descricao);
            $command->error($th);
        }
    }

    /**
     * This function saves order products and their details to the database.
     *
     * @param order The order object that the order products will be associated with.
     * @param orderProducts An array of order products, where each order product is an array containing
     * information about a product in the order, such as its name, quantity, unit, unit price, total liquid
     * price, discount, and the ID of the corresponding BMS product.
     *
     * @return an array of OrderProducts.
     */
    public function saveOrderProduct($orderId, Request $request)
    {
        try {
            $newOrderProducts = array();
            $requestOrderProducts = $request->orderProducts ?? null;

            if (!$orderId || !$requestOrderProducts) {
                throw new exception('The given data was invalid. Missing parameters: OrderProducts', 422);
            }

            foreach ($requestOrderProducts as $requestOrderProduct) {
                if (is_int($requestOrderProduct)) {

                    $orderProduct      = $this->getOrderProductById($requestOrderProduct);
                    $bmsProduct        = $orderProduct->bms_product;
                    $newOrderProductId = $this->createOrderProduct($orderProduct->name, $orderProduct->quantity, $orderProduct->sale_unit, $orderProduct->sale_price,  $orderProduct->unit, $orderProduct->unit_price, $orderProduct->total_liquid_price, $orderId, $orderProduct->correction_price_percent ?? 0.0, $orderProduct->discount_percent ?? 0.0, $orderProduct->discount_value, $bmsProduct, $orderProduct->conversion, $orderProduct->volume);
                } else {
                    $bmsProduct        = $this->getBmsProductById($requestOrderProduct['bms_product']);
                    $productValues     = $this->calcOrderProductValues($requestOrderProduct, $bmsProduct->avg_price);
                    $newOrderProductId = $this->createOrderProduct(
                        $bmsProduct->name,
                        $requestOrderProduct['quantity'],
                        $requestOrderProduct['volume'],
                        $requestOrderProduct['price'],
                        $bmsProduct->sell_unit,
                        $bmsProduct->avg_price,
                        $productValues[1],
                        $orderId,
                        $requestOrderProduct['correctionPrice'] ?? 0.0,
                        $requestOrderProduct['discount'] ?? 0.0,
                        $productValues[0],
                        $bmsProduct->id,
                        $requestOrderProduct['conversion'] ?? null,
                        $requestOrderProduct['volume'] ?? null,
                        null
                    );
                }
                $newOrderProduct = $this->getOrderProductById($newOrderProductId);
                array_push($newOrderProducts, $newOrderProduct);
            }

            return $newOrderProducts;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function createOrderProduct($name, $quantity, $saleUnit, $salePrice, $unit, $unitPrice, $totalLiquidPrice, $orderId, $correctionPricePercent, $discountPercent, $discountValue, $bmsProduct, $conversion = null, $volume = null, $unavailability = null)
    {
        try {
            return OrderProducts::create(
                [
                    'name'                     => $name,
                    'quantity'                 => $quantity,
                    'sale_unit'                => $saleUnit,
                    'sale_price'               => $salePrice,
                    'unit'                     => $unit,
                    'unit_price'               => $unitPrice,
                    'total_liquid_price'       => $totalLiquidPrice,
                    'order_id'                 => $orderId,
                    'correction_price_percent' => $correctionPricePercent,
                    'discount_percent'         => $discountPercent,
                    'discount_value'           => $discountValue,
                    'bms_product'              => $bmsProduct,
                    'conversion'               => $conversion,
                    'volume'                   => $volume,
                    'unavailability'           => $unavailability
                ]
            )->id;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    /**
     * The function calculates the total price, discount value, and liquid value of a product in an order.
     *
     * @param orderProduct It is an array that contains information about a product in an order, including
     * its price, quantity, and discount percentage.
     *
     * @return An array containing the total price, discount value, and product liquid value of an order
     * product.
     */
    public function calcOrderProductValues($orderProduct, $avg_price)
    {
        try {
            $productValues      = [];
            $discountValue      = $avg_price - $orderProduct['price'];
            $productLiquidValue = $orderProduct['price'] * $orderProduct['quantity'];

            array_push($productValues, $discountValue, $productLiquidValue);

            return $productValues;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }

    public function updateOrderProducts($orderId, $data)
    {
        try {
            if (empty($data['products'])) {
                throw new exception('No OrderProducts found!', 422);
            }

            $order = $this->order->find($orderId);
            $exists = $this->order->where('id', $orderId)->exists();
            if (!$order || !$exists) {
                throw new exception('No order found with that id!', 500);
            }

            foreach ($data['products'] as $product) {
                if ($order->orderProducts()->where('id', $product['id'])->exists()) {
                    $orderProduct = OrderProducts::find($product['id']);

                    if (!empty($product['notes'])) {
                        $orderProduct->notes = $product['notes'];
                    }

                    if (!empty($product['conversion'])) {
                        $orderProduct->conversion = $product['conversion'];
                        $orderProduct->total_liquid_price = $product['conversion'] * $orderProduct->sale_price;
                    }

                    if (!empty($product['volume'])) {
                        $orderProduct->volume = $product['volume'];
                    }

                    if (!empty($product['unavailability'])) {
                        $orderProduct->unavailability = $product['unavailability'];
                    }

                    if (!empty($product['batch'])) {
                        $orderProduct->bms_product_batch = $product['batch'];
                    }

                    $orderProduct->save();
                }
            }
        } catch (Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }

    public function updateOrAddProducts($order, $products)
    {
        try {
            $orderProducts = [];
            foreach ($order->orderProducts()->get('id') as $product) {
                array_push($orderProducts, $product->id);
            }

            $requestOrderProducts = [];
            foreach ($products as $product) {
                if (!empty($product['order_products_id'])) {
                    array_push($requestOrderProducts, $product['order_products_id']);
                }
            }

            foreach (array_diff($orderProducts, $requestOrderProducts) as $productToDelete) {
                $orderProduct = OrderProducts::find($productToDelete);
                $orderProduct->delete();
            };

            foreach ($products as $product) {
                if (!empty($product['order_products_id'])) {
                    $orderProduct = OrderProducts::find($product['order_products_id']);
                    $bmsProduct = $this->bmsProducts->where('id', $product['bms_product'])->first();
                    $productValues = $this->calcOrderProductValues($product, $bmsProduct->avg_price);

                    $orderProduct->quantity = $product['quantity'];
                    $orderProduct->volume = $product['volume'];
                    $orderProduct->sale_price = $product['price'];
                    $orderProduct->correction_price_percent = $product['correctionPrice'];
                    $orderProduct->discount_percent = $product['discount'];
                    $orderProduct->discount_value = $productValues[0];
                    $orderProduct->save();

                    continue;
                }

                $bmsProduct = $this->bmsProducts->where('id', $product['bms_product'])->first();
                $productValues = $this->calcOrderProductValues($product, $bmsProduct->avg_price);

                $this->createOrderProduct(
                    $bmsProduct->name,
                    $product['quantity'],
                    $product['volume'],
                    $product['price'],
                    $bmsProduct->sell_unit,
                    $bmsProduct->avg_price,
                    $productValues[1],
                    $order->id,
                    $product['correctionPrice'] ?? 0.0,
                    $product['discount'] ?? 0.0,
                    $productValues[0],
                    $bmsProduct->id,
                    $product['conversion'] ?? null,
                    $product['volume'] ?? null,
                    null
                );
            }
        } catch (\Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }

    public function getBmsProductById($productId)
    {
        try {
            return $this->bmsProducts->with('orderProduct')->where('id', $productId)->first();
        } catch (Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }

    public function getOrderProductById($productId)
    {

        try {
            return $this->orderProduct->with('bmsProduct')->where('id', $productId)->first();
        } catch (Exception $e) {
            throw new exception($e->getMessage(), 500);
        }
    }
}
