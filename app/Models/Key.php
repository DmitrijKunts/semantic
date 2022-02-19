<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['id', 'cat_id', 'name'];

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }
}
