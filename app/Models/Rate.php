<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = ['raterid', 'ratedid', 'rate'];

    public function rater()
    {
        return $this->belongsTo(User::class, 'raterid');
    }

    public function rated()
    {
        return $this->belongsTo(User::class, 'ratedid');
    }

    public static function saveOrUpdate($raterId, $ratedId, $rate)
    {
        $existingRate = self::where('raterid', $raterId)
                            ->where('ratedid', $ratedId)
                            ->first();

        if ($existingRate) {
            self::where('raterid', $raterId)
                ->where('ratedid', $ratedId)
                ->update(['rate' => $rate]);
        } else {
            self::create([
                'raterid' => $raterId,
                'ratedid' => $ratedId,
                'rate' => $rate,
            ]);
        }
    }
}
