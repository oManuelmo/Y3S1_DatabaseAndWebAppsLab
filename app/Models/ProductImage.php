<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images'; 

    public $timestamps = false;

    protected $fillable = [
        'itemid',
        'imageid',
    ];
    public $incrementing = false;
    protected $primaryKey = null;
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'itemid', 'itemid');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'imageid', 'imageid');
    }
}


