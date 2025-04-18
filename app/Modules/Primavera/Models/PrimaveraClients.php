<?php

namespace App\Modules\Primavera\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrimaveraClients extends Model
{
    use HasFactory;

    protected $table = 'primavera_clients';
    protected $primaryKey = 'id';

    protected $fillable = [
        "primavera_id",
        'name',
        'address',
        'postal_code',
        'postal_code_address',
        'country',
        'tax_number',
        'phone_1',
        'phone_2',
        'phone_3',
        'payment_method',
        'payment_condition',
        'email',
        'total_debt',
        'age_debt',
        'status',
        'rec_mode',
        'fiscal_name',
        'notes',
        'zone',
        'zone_description',
        'discount_1',
        'discount_2',
        'discount_3'
    ];

    /**
     * > This function returns all the invoices that belong to this client
     *
     * @return A collection of PrimaveraInvoices
     */
    public function invoices()
    {
        return $this->hasMany(PrimaveraInvoices::class, 'primavera_client', 'id');
    }
}
