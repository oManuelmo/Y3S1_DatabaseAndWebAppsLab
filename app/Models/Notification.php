<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $primaryKey = 'notificationid';

    protected $fillable = [
        'userid',
        'type',
        'bidid',
        'itemid',
        'itemname',
        'transactionid',
        'datetime',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid', 'userid');
    }

    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class, 'bidid', 'bidid');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'itemid', 'itemid');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transactionid', 'transactionid');
    }
}