<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps  = false;
    protected $primaryKey = 'userid';
    protected $rememberTokenName = 'remembertoken';

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'isadmin',
        'rate',
        'address',
        'city',
        'country',
        'postalcode',
        'phone',
        'balance',
        'bidbalance',
        'imageid',
        'birthdate',
        'bantime',
        'bannedreason',
    ];


    protected $hidden = [
        'password',
        'rememberToken',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'isadmin' => 'boolean',
        'rate' => 'decimal:2',
        'balance' => 'decimal:2',
        'bidbalance' => 'decimal:2',
        'birthdate' => 'date',
        'bantime' => 'datetime',
    ];

    public function ratings()
    {
        return $this->hasMany(Rate::class, 'ratedid', 'userid');
    }
    public function ratedBy()
    {
        return $this->hasMany(Rate::class, 'raterid', 'userid');
    }
    public function averageRating()
    {
        $average = $this->ratings()->avg('rate');
        return $average ?: 0;
    }

    public function updateProfile(array $data)
    {
        $this->update($data);
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'imageid', 'imageid'); 
    }
    
    public function isBanned(): bool
    {
        return $this->bantime && now()->lessThan($this->bantime);
    }
}