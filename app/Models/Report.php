<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';
    protected $primaryKey = 'reportid';
    public $timestamps = false;

    protected $fillable = [
        'reportedauction',
        'userid',
        'type',
        'reporttext',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'reportedauction', 'itemid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userid', 'userid');
    }
    public function getItem(): ?Item
    {
        return $this->item;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
}
