<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Chat extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'chatid';
    protected $fillable = [
        'userid',
        'statustype',
        'createdat',
        'updatedat',
        'adminid',
    ];

    protected $casts = [
        'updatedat' => 'datetime',  
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userid', 'userid');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'adminid', 'userid');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'chatid', 'chatid');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'chatid', 'chatid')->latest();
    }
}
