<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasUUID, SoftDeletes;

    protected $fillable = ['item_id', 'name', 'user_id'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
