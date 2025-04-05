<?php

namespace App\Modules\Feedback\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackWho extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_who';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'feedback_id'
    ];

    public function feedback() : BelongsTo {
        return $this->belongsTo(FeedbackWho::class,);
    }

    public function findWhoByFeedbackId(int $id){
        return $this->where('feedback_id', $id);
    }
}
