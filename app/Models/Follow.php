<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    protected $table = 'follows';
    protected $primaryKey = null; 
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['followerid', 'itemid'];

    public function user()
    {
        return $this->belongsTo(User::class, 'followerid', 'userid'); 
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'itemid', 'itemid');
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public static function getFollow($followerId, $itemId)
    {
        return self::where('followerid', $followerId) 
            ->where('itemid', $itemId)
            ->first();
    }
}
