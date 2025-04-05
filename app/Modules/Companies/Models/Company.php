<?php
namespace App\Modules\Companies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'img_url',
        'phone',
        'notification_time',
        'automatic_notification',
        'api_key_maps'
    ];

    protected $casts = [
        'automatic_notification' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function setAutomaticNotificationAttribute($value)
    {
        $this->attributes['automatic_notification'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
