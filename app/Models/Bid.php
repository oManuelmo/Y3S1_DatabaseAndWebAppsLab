<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    use HasFactory;

    protected $table = 'bids'; 

    protected $primaryKey = 'bidid'; 

    public $timestamps = false;

    protected $fillable = [
        'bidderid' => 'required|numeric',
        'itemid' => 'required|numeric',
        'value' => 'required|numeric|min:0',
        'time' => 'required|datetime',
    ];

    public function bidder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bidderid', 'userid');
    }


    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'itemid', 'itemid');
    }
}


