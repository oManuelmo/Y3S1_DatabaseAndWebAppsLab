<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $primaryKey = 'messageid';
    protected $fillable = [
        'chatid',
        'senderid',
        'messagetext',
        'createdat',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chatid', 'chatid');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'senderid', 'userid');
    }
}
