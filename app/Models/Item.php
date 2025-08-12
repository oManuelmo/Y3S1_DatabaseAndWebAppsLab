<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Item extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'items';

    protected $primaryKey = 'itemid';

    protected $fillable = [
        'name',
        'initialprice',
        'soldprice',
        'width',
        'height',
        'description',
        'starttime',
        'duration',
        'deadline',
        'style',
        'theme',
        'technique',
        'ownerid',
        'topbidder',
        'artistid',
        'state',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ownerid', 'userid');
    }


    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artistid', 'artistid');
    }


    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'itemid', 'itemid');
    }


    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'itemId', 'imageId');
    }

    public function topBidder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'topbidder', 'userid');
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }
}

