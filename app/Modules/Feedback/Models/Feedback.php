<?php

namespace App\Modules\Feedback\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'feedback';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'patient_number',
        'reason',
        'date',
        'time',
        'description'
    ];

    public function feedbackWho() : HasMany {
        return $this->hasMany(FeedbackWho::class, 'feedback_id');
    }
}
