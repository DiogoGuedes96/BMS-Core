<?php

namespace App\Modules\Orders\Models;

use App\Modules\Clients\Models\AddressZone;
use App\Modules\Clients\Models\Clients;
use App\Modules\Primavera\Models\PrimaveraInvoices;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'description',
        'writen_date',
        'delivery_date',
        'delivery_period',
        'delivery_address',
        'writen_by',
        'requested_by',
        'prepared_by',
        'invoiced_by',
        'erp_invoice_id',
        'bms_client',
        'total_iva_value',
        'total_value',
        'total_liquid_value',
        'priority',
        'caller_phone',
        'bms_address_zone_id',
        'request_number',
        'parent_order'
    ];

    /**
     * > This function returns the client associated with the current order
     *
     * @return A Modules\Clients\Entities\Client;
     */
    public function client()
    {
        return $this->belongsTo(Clients::class, 'bms_client', 'id');
    }

    /**
     * > This function returns all the invoices associated with the current order
     *
     * @return A Modules\Primavera\Entities\PrimaveraInvoices;
     */
    public function primaveraInvoices()
    {
        return $this->belongsTo(PrimaveraInvoices::class, 'erp_invoice_id', 'id');
    }

    /**
     * This function returns all the orderProducts that are in the current order
     *
     * @return A collection of OrderProducts.
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProducts::class, 'order_id', 'id');
    }

    /**
     * It returns the user who wrote the current order.
     *
     * @return The user who wrote the current order.
     */
    public function orderWriter()
    {
        return $this->belongsTo(User::class, 'id', 'writen_by');
    }

    /**
     * It returns the user who requested the current order.
     *
     * @return The user who requested the current order.
     */
    public function orderRequester()
    {
        return $this->belongsTo(User::class, 'id', 'requested_by');
    }

    /**
     * It returns the user who prepared the current order.
     *
     * @return The user who prepared the current order.
     */
    public function orderPreparer()
    {
        return $this->belongsTo(User::class, 'id', 'prepared_by');
    }

    /**
     * It returns the user who invoiced the current order.
     *
     * @return The user who invoiced the current order.
     */
    public function orderInvoicer()
    {
        return $this->belongsTo(User::class, 'id', 'invoiced_by');
    }

    public function parentOrder()
    {
        return $this->belongsTo(Order::class, 'parent_order', 'id');
    }

    public function getOrdersByStatusAndPriority($status)
    {
        return $this
                ->with('zone')
                ->with('client')
                ->has('orderProducts')
                ->with(['orderProducts' => function ($query) {
                    $query->whereNull('unavailability')
                        ->with('bmsProduct')
                        ->with('bmsProduct.batches')
                        ->orWhere('unavailability', '=', false);
                }])
                ->where('status', $status)
                ->orderBy('delivery_date', 'asc')
                ->orderByRaw("CASE
                    WHEN delivery_period = 'morning' THEN 1
                    WHEN delivery_period = 'evening' THEN 2
                    ELSE 3
                    END")
                ->orderByRaw("CASE
                    WHEN priority IS NULL THEN 1
                    ELSE 0
                    END")
                ->orderBy('priority', 'asc')
                ->orderBy('created_at', 'asc')
                ->paginate(50);
    }

    public function getOrdersByStatus($status)
    {
        return $this
                ->with(['orderProducts' => function ($query) {
                    $query->with('bmsProduct')
                         ->with('bmsProduct.batches');
                }])
                ->with('zone')
                ->with('client')
                ->with('client.addresses')
                ->where('status', $status)
                ->orderBy('delivery_date', 'desc')
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
    }

    public function zone(){
        return $this->hasOne(AddressZone::class, 'id', 'bms_address_zone_id');
    }
}
