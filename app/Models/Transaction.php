<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'auctionid',
        'userid',
        'transactiontype',
        'value',
        'time',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid', 'userid');
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'auctionid', 'itemid');
    }
}