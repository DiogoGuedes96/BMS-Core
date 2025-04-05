<?php

namespace App\Modules\Orders\Controllers;

use Exception;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Modules\Clients\Models\Clients;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Requests\FilterOrderProductsRequest;
use App\Modules\Orders\Requests\ForkOrderRequest;
use App\Modules\Orders\Requests\SaveOrderRequest;
use App\Modules\Orders\Requests\UpdateOrderPending;
use App\Modules\Orders\Requests\UpdateOrderRequest;
use App\Modules\Orders\Requests\ValidateStockOrderRequest;
use App\Modules\Orders\Services\OrderProductsService;
use App\Modules\Orders\Services\OrdersService;

class OrdersController extends Controller
{
    private $ordersService;
    private $orderProductsService;
    private $orders;

    public function __construct()
    {
       $this->ordersService        = new OrdersService();
       $this->orderProductsService = new OrderProductsService();
       $this->orders               = new Order();
    }

    public function store(SaveOrderRequest $saveOrderRequest)
    {
        try {
            $newOrder = $this->ordersService->saveNewOrder($saveOrderRequest);
            $this->orderProductsService->saveOrderProduct($newOrder->id, $saveOrderRequest);
            $order = $this->ordersService->getOrderById($newOrder->id);
            return response()->json(['order' => $order]);

        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function update(UpdateOrderRequest $updateOrderRequest, $id)
    {
        try {
            if (!$id) {
                throw new Exception('Invalid Order Id!', 400);
            }

            $orderData = $updateOrderRequest->orderData;
            $updateValues = $updateOrderRequest->values;

            if (!$orderData && !$updateValues) {
                throw new Exception('Missing parameters!', 400);
            }
            $this->ordersService->updateOrder($id, $updateValues);
            $this->orderProductsService->updateOrderProducts($id, $orderData);

            $order = $this->ordersService->getOrderById($id, 'orderProducts');

            return response()->json(['order' => $order]);

        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function updateOrderAndAddProducts($id, Request $request)
    {
        try {
            if (!$id) {
                throw new Exception('Invalid Order Id!', 400);
            }

            $order = Order::find($id);

            if (!$order) {
                throw new exception('No order found with that id!', 500);
            }

            $orderUpdate = [
                "caller_phone" => !empty($request->callerPhone) ? $request->callerPhone : null,
                "notes" => !empty($request->orderNotes) ? $request->orderNotes : null,
                "bms_client" => !empty($request->bmsClient) ? $request->bmsClient : null
            ];

            $this->ordersService->updateOrder($id, $orderUpdate);
            $this->orderProductsService->updateOrAddProducts($order, $request->orderProducts);

            $order = $this->ordersService->getOrderById($id, 'orderProducts');

            return response()->json(['order' => $order]);

        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getAllOrders()
    {
        try {
            return [
                'orders' => $this->ordersService->getAllOrders(),
            ];
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getProductsByOrders($clientId)
    {
        try {
            $orders = $this->ordersService->getProductsOrdersByClientId($clientId);

            return response()->json($orders);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getOrdersDetailsByClientId($clientId) {
        try {
            return [
                'products' => $this->ordersService->getProductsOrdersByClientId($clientId),
                'orders' => $this->getClientFilteredOrders($clientId)
            ];
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getClientFilteredOrders(Clients $bmsClient)
    {
        try {
            return $this->ordersService->getClientFilteredOrders($bmsClient);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getProductsMostBoughtProductsByClient(Clients $bmsClient, FilterOrderProductsRequest $filters){
        try{
            $products = $this->ordersService->getBoughtProductsByClient($bmsClient->id, $filters, 'desc');

            return ['products' => $products];
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }


    public function getLessBoughtProductsByClient(Clients $bmsClient, FilterOrderProductsRequest $filters){
        try{
            $products = $this->ordersService->getBoughtProductsByClient($bmsClient->id, $filters, 'asc');

            return ['products' => $products];
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getOrderById($orderId)
    {
        try{
            return $this->ordersService->getOrderById($orderId);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function setPriorityOrder($orderId)
    {
        try {
            $order = $this->ordersService->setPriorityOrder($orderId);

            return response()->json(['message' => 'success', 'order' => $order]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function forkOrder(ForkOrderRequest $forkOrderRequest)
    {
        try {
            $newOrder = $this->ordersService->saveNewOrder($forkOrderRequest);
            $newOrderProducts = $this->orderProductsService->saveOrderProduct($newOrder->id, $forkOrderRequest);

            $this->ordersService->softDeleteProductsFromOrder($forkOrderRequest);

            $parentOrder = $this->ordersService->getOrderById($forkOrderRequest->orderId);

            return response()->json([
                'ParentOrder' => ['order' => $parentOrder],
                'NewOrder'    => ['order' => $newOrder, 'orderProducts' => $newOrderProducts]
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function removeProductsFromOrder(Request $request)
    {
        try {
            $this->ordersService->softDeleteProductsFromOrder($request);
            $order = $this->ordersService->getOrderById($request->orderId);

            return response()->json([
                'Order' => ['order' => $order]
            ]);

        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function setOrderStatus($orderId, $status)
    {
        try{
            $orderID = $this->ordersService->setOrderStatus($orderId, $status);
            $order = $this->ordersService->getOrderById($orderID);

            return response()->json(['message' => 'success', 'Order Status updated with Success' => ['Order: ' => $order->id, 'Status' => $status]]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getOrdersByStatus($status)
    {
        try{
            $orderList = $this->ordersService->getAllOrdersByStatus($status);

            return response()->json(['message' => 'success', 'Orders' => $orderList]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function searchOrdersByInput($input){
        try{
            $orderList = $this->ordersService->searchOrdersByInput($input);

            return response()->json(['message' => 'success', 'orders' => $orderList]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function validateStock(Order $order, ValidateStockOrderRequest $request) {
        try {
            if (!$order){
                return response()->json(['message' => 'error', 'error' => 'Something went Wrong !'], 500);
            }

            $orderData = $request->all();

            $this->ordersService->validateStock($order, $orderData);

            $order = $this->ordersService->setOrderStatus($order->id, $orderData['status']);

            return response()->json(['message' => 'success', 'Order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function updateOrderPending(Order $order, UpdateOrderPending $request)
    {
        try {
            if (!$order){
                return response()->json(['message' => 'error', 'error' => 'Something went Wrong !'], 500);
            }

            $orderData = $request->all();

            $order = $this->ordersService->updateOrderPending($order, $orderData);

            return response()->json(['message' => 'success', 'Order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function generatePDFOrder(Order $order)
    {
        try {
            if (!$order){
                return response()->json(['message' => 'error', 'error' => 'Something went Wrong !'], 500);
            }

            $data = $this->ordersService->generatePDF($order);

            $html = view('orders::invoice', $data)->render();

            $pdf = new Dompdf();
            $pdf->setPaper('A4');
            $pdf->loadHtml($html);
            $pdf->render();

            return $pdf->stream('invoice.pdf');
        } catch (\Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
