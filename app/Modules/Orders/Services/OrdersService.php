<?php

namespace App\Modules\Orders\Services;

use App\Modules\Clients\Models\Address;
use App\Modules\Clients\Models\Clients;
use App\Modules\Clients\Services\AddressZoneService;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderProducts;
use App\Modules\Users\Models\User;
use DateTime;
use Exception;
use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrdersService
{
    private $user;
    private $order;
    private $address;
    private $orderProducts;
    private $orderProductsService;
    private $bmsClient;
    private $addressZoneService;

    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_PARTIALLY_SHIPPED = 'partially_shipped';
    const STATUS_IN_PREPARATION = 'preparing';
    const STATUS_IN_DELIVERY = 'delivering';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';

    public function __construct()
    {
        $this->user          = new User();
        $this->order         = new Order();
        $this->address       = new Address();
        $this->orderProducts = new OrderProducts();
        $this->orderProductsService = new OrderProductsService();
        $this->bmsClient     = new Clients();
        $this->addressZoneService     = new AddressZoneService();
    }

    public function getAllOrders()
    {
        try {
            return $this->order->orderByRaw("priority IS NULL, priority DESC, created_at ASC")->get();
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error', 'error' => 'Something went wrong'], 500);
        }
    }

    /**
     * It updates or creates an order based on the invoice number
     *
     * @param invoice The invoice object from the BMS API
     * @param bmsClient The client object from the BMS database.
     * @param command The command that is being executed.
     *
     * @return The order that was created or updated.
     */
    public function saveInvoiceOrder($invoice, $bmsClient, $command)
    {
        try {
            $userId = 1;
            $user = $this->user->where('name', 'admin')->first();
            if ($user) {
                $userId = $user->id;
            }

            $address = $bmsClient->addresses->first()->address ?? "";
            $order = Order::updateOrCreate(
                ['erp_invoice_id' => $invoice->id],
                [
                    'status'           => $this::STATUS_COMPLETED,
                    'description'      => 'Encomenda criada automaticamente, baseada em uma fatura(FA), previa ao sistema BMS.',
                    'writen_by'        => $userId,
                    'requested_by'     => $userId,
                    'writen_date'      => $invoice->invoice_date ?? "",
                    'prepared_by'      => $userId,
                    'delivery_date'    => $invoice->invoice_date ?? "",
                    'delivery_address' => $address,
                    'invoiced_by'      => $userId,
                    'total_value'      => $invoice->total_value,
                    'total_liquid_value' => $invoice->liquid_value,
                    'total_iva_value'  => $invoice->iva_value,
                    'bms_client'       => $bmsClient->id,
                ]
            );

            $command->warn('Order ' . $order->id . ' created for invoice ' . $invoice->number);
            return $order;
        } catch (\Throwable $th) {
            $command->error('Error to save order from invoice ' . $invoice->NumDoc);
            $command->error($th);
            return false;
        }
    }

    public function getProductsOrdersByClientId(Clients $client)
    {
        try {
            $orders = Order::withCount('orderProducts')
                ->with(['orderProducts' => function ($query) {
                    $query->take(100);
                }])
                ->where('bms_client', $client->id)
                ->orderBy('writen_date', 'asc')
                ->get();

            $products = collect([]);

            foreach ($orders as $order) {
                $orderProducts = collect($order->orderProducts->toArray())->map(function ($product) use ($order) {
                    return array_merge(['order_at' => $order->writen_date], $product);
                });

                $products = $products->merge($orderProducts);
            }

            $products = $products->take(100)->toArray();
            return $products;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getAllClientOrders(Clients $bmsClient)
    {
        try{
            return $bmsClient->orders()->with('primaveraInvoices')->get();
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getClientFilteredOrders(Clients $bmsClient)
    {
        try {
            $orders = $this->getAllClientOrders($bmsClient);

            $ordersArr = array();

            $ordersArr[0]['months'] = 1;
            $month0 = $orders->whereBetween('delivery_date', [Carbon::now()->subMonths(2), Carbon::now()->subMonths(1)]);
            $ordersArr[0]['fa'] = array();

            foreach ($month0 as $month) {

                array_push($ordersArr[0]['fa'], $month);
            }

            $ordersArr[1]['months'] = 2;
            $month1 = $orders->whereBetween('delivery_date', [Carbon::now()->subMonths(3), Carbon::now()->subMonths(2)]);
            $ordersArr[1]['fa'] = array();

            foreach ($month1 as $month) {
                array_push($ordersArr[1]['fa'], $month);
            }

            $ordersArr[2]['months'] = 3;
            $month2 = $orders->where('delivery_date', '<', Carbon::now()->subMonths(3)); // Three month orders
            $ordersArr[2]['fa'] = array();

            foreach ($month2 as $month) {
                array_push($ordersArr[2]['fa'], $month);
            }

            return $ordersArr;
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getBoughtProductsByClient($clientId, $filters, $sortOrder)
    {
        $startDate = $filters->get('startDate');
        $endDate = $filters->get('endDate');
        $numProducts = $filters->get('numProducts');

        $products = Products::withCount(['orderProduct as total_quantity' => function ($query) use ($clientId, $startDate, $endDate) {
            $query->select(DB::raw('SUM(quantity)'))
                ->whereIn('order_id', function ($query) use ($clientId, $startDate, $endDate) {
                    $query->select('id')
                        ->from('orders')
                        ->where('bms_client', $clientId);

                    if ($startDate) {
                        $query->whereBetween('orders.created_at', [
                            Carbon::parse($startDate)->startOfDay(),
                            Carbon::parse($endDate ?? $startDate)->endOfDay()
                        ]);
                    }
                });
        }])
            ->with('prices', 'images')
            ->having('total_quantity', '>', 0)
            ->orderBy('total_quantity', $sortOrder);

        if ($numProducts) {
            $products->take($numProducts);
        }

        return $products->get();
    }

    public function saveNewOrder($request)
    {
        try {
            $user = Auth::user();
            $requestOrderProducts = $request->orderProducts ?? null;

            $parentOrderId  = $request->orderId ?? null;

            if ($parentOrderId) {
                $order = $this->getOrderById($parentOrderId) ?? null;

                return $this->createOrder(
                    $this::STATUS_PARTIALLY_SHIPPED,
                    $order->description,
                    $order->writen_by,
                    $order->requested_by,
                    $order->writen_date,
                    $order->prepared_by,
                    $order->delivery_date,
                    $order->delivery_address,
                    $order->erp_invoice_id,
                    $order->invoiced_by,
                    $order->total_iva_value,
                    $order->total_value,
                    $order->total_liquid_value,
                    $order->bms_client,
                    $order->caller_phone,
                    $order->request_number,
                    $parentOrderId,
                    $order->bms_address_zone_id,
                );
            }

            $bmsClientId = $request->bmsClient ?? null;
            $phone       = $request->callerPhone ?? null;

            if ($bmsClientId && !$phone) {
                $bmsClient = $this->bmsClient->where('id', $bmsClientId)->first();
                $phone = $this->bmsClient->where('id', $bmsClientId)
                ->where(function ($query) {
                    $query->whereNotNull('phone_1')
                    ->orWhereNotNull('phone_2')
                    ->orWhereNotNull('phone_3');
                })
                ->selectRaw("COALESCE(phone_1, phone_2, phone_3) as phone")
                ->value('phone');
                $phone = intval($phone);
            }

            if (!$requestOrderProducts && !$bmsClientId && !$phone) {
                throw new exception('The given data was invalid. Missing parameteres !', 422);
            }

            return $this->createOrder(
                $this::STATUS_NEW,
                $request->orderNotes ?? $bmsClient->notes ?? null,
                $user->id,
                $user->id,
                now(),
                $user->id,
                null,
                null,
                null,
                $user->id,
                0.0,
                0.0,
                0.0,
                $bmsClientId ?? null,
                $phone,
                null,
                null,
                null,
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function createOrder($status, $description, $writenBy, $requestedBy, $writenDate, $preparedBy, $deliveryDate, $deliveryAddress, $erpInvoiceId, $invoicedBy, $totalIvaValue, $totalValue, $totalLiquid, $bmsClient, $callerPhone, $requestNumber, $parentOrder, $zone)
    {
        try {
            return Order::Create(
                [
                    'status'             => $status,
                    'description'        => $description,
                    'writen_by'          => $writenBy,
                    'requested_by'       => $requestedBy,
                    'writen_date'        => $writenDate,
                    'prepared_by'        => $preparedBy,
                    'delivery_date'      => $deliveryDate,
                    'delivery_address'   => $deliveryAddress,
                    'erp_invoice_id'     => $erpInvoiceId,
                    'invoiced_by'        => $invoicedBy,
                    'total_iva_value'    => $totalIvaValue,
                    'total_value'        => $totalValue,
                    'total_liquid_value' => $totalLiquid,
                    'bms_client'         => $bmsClient,
                    'caller_phone'       => $callerPhone,
                    'request_number'     => $requestNumber,
                    'parent_order'       => $parentOrder,
                    'bms_address_zone_id'=> $zone
                ]
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    public function getClientPhone($callerPhone, $phone_1, $phone_2, $phone_3)
    {
        try {
            return $callerPhone ?? $phone_1 ?? $phone_2 ?? $phone_3;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function updateOrder($id, $data)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                throw new exception('No order found with that id!', 500);
            }

            $updateData = [];
            if (!empty($data['addressId'])) {
                $address = Address::where('id', $data['addressId'])->first();
                if ($address) {
                    $address->selected_delivery_address = true;
                    $address->save();

                    $updateData['delivery_address'] = $address->address . " " . $address->postal_code_address . " " . $address->postal_code;
                }
            }

            if (empty($data['addressId']) && !empty($data['address'])) {
                $updateData['delivery_address'] = $data['address'];
            }

            if (array_key_exists('priority', $data) && is_bool($data['priority'])) {
                $updateData['priority'] = $data['priority'] === true ? NOW() : null;
            }

            if (!empty($data['notes'])) {
                $updateData['description'] = $data['notes'];
            }

            if (!empty($data['request'])) {
                $updateData['request_number'] = $data['request'];
            }

            if (!empty($data['zona'])) {
                $zone = $this->addressZoneService->findOrCreateZone($data['zona']);
                $updateData['bms_address_zone_id'] = $zone;
            }

            if (!empty($data['caller_phone'])) {
                $updateData['caller_phone'] = $data['caller_phone'];
            }

            if (!empty($data['delivery_date'])) {
                $dateTime = DateTime::createFromFormat("Y-m-d\TH:i:s.u\Z", $data['delivery_date']);

                $updateData['delivery_date'] = $dateTime->format("Y-m-d");;
            }

            if (!empty($data['delivery_period'])) {
                $updateData['delivery_period'] = $data['delivery_period'];
            }

            if (!empty($data['bms_client'])) {
                $updateData['bms_client'] = $data['bms_client'];
            }

            $updateData['status'] = $this::STATUS_PENDING;

            $order->update($updateData);
            return $order;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    /**
     * The function calculates the total order values including the total IVA value, total value, and total
     * liquid value of the given order products.
     *
     * @param orderProducts It is an array of objects representing the products in an order. Each object
     * contains information about the product, such as its price and tax (IVA) rate. The function
     * calculates and returns various values related to the order, such as the total value, total tax
     * value, and total liquid value (total
     *
     * @return an array containing three key-value pairs: 'total_iva_value', 'total_value', and
     * 'total_liquid_value'. The 'total_iva_value' key contains the total value of the IVA tax for all
     * products in the order, the 'total_value' key contains the total value of all products in the order,
     * and the 'total_liquid_value' key contains
     */
    public function calcOrderValues($orderProducts)
    {
        try {
            $orderValues     = [];
            $orderIvaValue   = 0;
            $totalOrderValue = 0;

            foreach ($orderProducts as $orderProduct) {
                $percentIva = intval($orderProduct['product']['iva']);
                if (is_numeric($orderProduct['price'])) {
                    if (is_numeric($percentIva)) {
                        $productPriceWithDiscount = $orderProduct['price'];

                        $totalProductValue = $productPriceWithDiscount * $orderProduct['quantity'];
                        $productIvaValue   = $totalProductValue * ($percentIva / 100);
                        $orderIvaValue += $productIvaValue;
                        $totalOrderValue += $totalProductValue;
                    }
                }
            }
            $totalLiquidValue = $totalOrderValue - $orderIvaValue;

            array_push($orderValues, $orderIvaValue, $totalOrderValue, $totalLiquidValue);

            return $orderValues;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function setPriorityOrder($orderId)
    {
        try {
            if (!$orderId) {
                throw new exception('Missing parameters: orderId', 400);
            }

            $order = $this->order->findOrFail($orderId);
            $order->update(
                ['priority' => now()]
            );

            if (!$order) {
                throw new exception('No Order found with given id!', 400);
            }

            return $order;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * The function sets the status of an order and returns the updated order or false if an error occurs.
     *
     * @param order The order parameter is an object representing an order in a system, likely an
     * e-commerce platform. It likely contains information such as the customer's name and contact
     * information, the items ordered, the total cost, and the current status of the order.
     * @param status The status parameter is the new status that we want to set for the order. It could be
     * a string or an integer depending on how the status is defined in the system. For example, it could
     * be "pending", "processing", "shipped", "delivered", etc.
     *
     * @return either the updated order object if the update was successful, or `false` if an error
     * occurred during the update.
     */
    public function setOrderStatus($orderId, $status)
    {
        try {
            if (!$orderId && !$status) {
                throw new exception('Missing parameters: Order, Status', 400);
            }

            $status = $this->getStatusValue($status);

            $update = ['status' => $status];

            if ($status == $this::STATUS_IN_DELIVERY) {
                $update['delivered_at'] = now();
            }

            $order  = $this->getOrderById($orderId);
            $order->update($update);

            return $order->id;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function softDeleteProductsFromOrder(Request $request)
    {
        try {
            $requestOrderId  = $request->orderId ?? null;
            $requestOrderProducts = $request->orderProducts ?? null;
            $order  = $this->getOrderById($requestOrderId);
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getOrderById($id, $relations = null)
    {
        try {
            if (!$id) {
                throw new Exception('The given order ID was invalid.', 422);
            }

            if ($relations) {
                return $this->order->with($relations)->where('id', $id)->first();
            }

            $order = $this->order
                ->with('client')
                ->with('client.addresses')
                ->with('client.addresses.zone')
                ->with('zone')
                ->with('parentOrder')
                ->with(['orderProducts' => function ($query) {
                    $query->whereNull('unavailability')
                        ->orWhere('unavailability', '=', false)
                        ->with('productBatch')
                        ->with('bmsProduct.images')
                        ->with('bmsProduct.prices')
                        ->with(['bmsProduct.batches' => function ($batchQuery) {
                            $batchQuery->where('active', 1);
                        }]);
                }])
                ->where('id', $id)
                ->first();
            if(!$order){
                throw new Exception('No Order Found with the given ID !', 404);
            }

            return $order;
        } catch (Exception $e){
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * This function retrieves all orders with a specific status and sorts them by delivery date, delivery
     * period, priority date, and creation date.
     *
     * @param status The status of the orders to retrieve. This function will return all orders with the
     * specified status.
     *
     * @return This function returns a collection of orders with their associated order products, filtered
     * by a given status and sorted by delivery date, delivery period, priority date, and creation date. If
     * an error occurs, it returns false.
     */
    public function getAllOrdersByStatus($status)
    {
        try {
            if (!$status) {
                throw new Exception('Missing Parameter: status!', 400);
            }

            if ($this->getStatusValue($status) === $this::STATUS_COMPLETED
                || $this->getStatusValue($status) === $this::STATUS_CANCELED) {
                $orderList = $this->order->getOrdersByStatus($status);
            } else {
                $orderList = $this->order->getOrdersByStatusAndPriority($status);
            }

            if (empty($orderList)) {
                throw new Exception('No Orders found !', 404);
            }

            if (!$orderList){
                throw new Exception('Something went Wrong !', 500);
            }
            return $orderList;
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function searchOrdersByInput($input){
        try{
            if (!$input){
                throw new Exception('Missing Parameter: input!', 422);
            }

            $orderList = $this->order
                ->with('orderProducts')
                ->with('client')
                ->where('id', $input)
                ->orWhereHas('client', function ($query) use ($input) {
                    $query->where('phone_1', $input)
                        ->orWhere('phone_2', $input)
                        ->orWhere('phone_3', $input)
                        ->orWhere('erp_client_id', $input)
                        ->orWhere('name', 'LIKE', '%' . $input . '%');
                })
                ->whereNotIn('status', ['new'])
                ->orderBy('created_at', 'desc')
                ->orderBy('delivery_date', 'desc')
                ->paginate(50);

            if (empty($orderList)){
                throw new Exception('No Orders found !', 404);
            }

            if (!$orderList){
                throw new Exception('Something went Wrong !', 500);
            }
            return $orderList;
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function validateStock(Order $order, $data)
    {
        try {
            $orderProducts = $order->orderProducts;

            foreach ($orderProducts as $product) {
                $product->unavailability = true;
                foreach ($data['products'] as $productId) {
                    if ($product->id == $productId) {
                        $product->unavailability = false;
                    }
                }

                $product->save();
            }

            return $orderProducts;
        } catch (\Exception $e) {
            throw new \Exception("Error Processing Request", 500);
        }
    }

    public function updateStatus(Order $order, $status)
    {
        try {
            $order->update(['status' => $this->getStatusValue($status)]);
            return $order;
        } catch (\Exception $e) {
            throw new \Exception("Error Processing Request", 500);
        }
    }

    private function getStatusValue($status)
    {
        switch ($status) {
            case 'new':
                return $this::STATUS_NEW;
                break;
            case 'pending':
                return $this::STATUS_PENDING;
                break;
            case 'partially_shipped':
                return $this::STATUS_PARTIALLY_SHIPPED;
                break;
            case 'preparing':
                return $this::STATUS_IN_PREPARATION;
                break;
            case 'delivering':
                return $this::STATUS_IN_DELIVERY;
                break;
            case 'completed':
                return $this::STATUS_COMPLETED;
                break;
            case 'canceled':
                return $this::STATUS_CANCELED;
                break;
            default:
                throw new Exception('Unacepted value in Status !', 422);
                break;
        }
    }

    public function updateOrderPending(Order $order, $data)
    {
        try {
            if (!empty($data['notes'])) {
                $order->update(['description' => $data['notes']]);
            }
            if (!empty($data['products'])) {
                $this->orderProductsService->updateOrderProducts($order->id, $data);
            }

            return $this->order->with('orderProducts')->find($order->id);
        } catch (\Exception $e) {
            throw new \Exception("Error Processing Request", 500);
        }
    }

    public function getOrderDetailsById($orderId)
    {
        return Order::with(
            'client',
            'client.addresses',
            'orderProducts',
            'orderProducts.bmsProduct',
            'orderProducts.bmsProduct.images',
            'orderProducts.bmsProduct.prices'
        )->find($orderId);
    }

    private function calculateTotalOrder($orderProducts)
    {
        $totalOrder = 0;
        foreach ($orderProducts as $orderProduct) {
            $totalOrder += $orderProduct->total_liquid_price;
        }
        return $totalOrder;
    }

    public function formattedValueToCurrency($value)
    {
        if (!$value) {
            $value = 0;
        }

        return number_format($value, 2, ',', '.');
    }

    public function generatePDF(Order $order)
    {
        try {
            $orderInfo = $this->getOrderById($order->id);

            $products = [];

            foreach ($orderInfo->orderProducts as  $product) {
                array_push($products, [
                    'name' => $product->name,
                    'quantity' => $product->quantity,
                    'unit' => $product->unit,
                    'sale_unit' => $product->sale_unit,
                    'volume' => $product->volume,
                    'unit_price' => $this->formattedValueToCurrency($product->unit_price),
                    'discount' => $this->formattedValueToCurrency($product->discount_value),
                    'total' => $this->formattedValueToCurrency($product->total_liquid_price),
                    'iva' => $product->bmsProduct->iva
                ]);
            }

            $imagePath = public_path('images/logotype.png');
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageSrc = 'data:image/jpeg;base64,' . $imageData;

            $client = $orderInfo->client;


            $deliveryDate = new DateTime($orderInfo->delivery_date);
            $formattedDeliveryDate = $deliveryDate->format('d/m/Y');

            $total = $this->formattedValueToCurrency(
                $orderInfo->total_liquid_price ??
                    $this->calculateTotalOrder($orderInfo->orderProducts) ??
                    0,
            );

            $data = [
                'client' => [
                    'name' => $client->name ?? null,
                    'contact' => $client->contact ?? null,
                    'erp_client_id' => $client->erp_client_id ?? null,
                    'address' => !empty($client->addresses[0]) ? $client->addresses[0]->address : null,
                ],
                'id' => $orderInfo->id,
                'notes' => $orderInfo->description,
                'caller_phone' => $orderInfo->caller_phone,
                'request_number' => $orderInfo->request_number,
                'delivery_date' => $formattedDeliveryDate,
                'delivery_period' => $orderInfo->delivery_period,
                'delivery_address' => $orderInfo->delivery_address,
                'products' => $products,
                'total' => $total,
                'image' => $imageSrc
            ];

            return $data;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
