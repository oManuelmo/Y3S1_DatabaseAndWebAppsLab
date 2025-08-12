<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'images';

    protected $primaryKey = 'imageid';

    protected $fillable = ['imageurl'];

    public function user()
    {
        return $this->hasOne(User::class);
    }


}
