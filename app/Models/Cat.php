<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cat extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['id', 'p_id', 'name', 'slug', 'text'];

    public function childs()
    {
        return $this->hasMany(Cat::class, 'p_id');
    }

    public function keys()
    {
        return $this->hasMany(Key::class, 'p_id');
    }

    public function parent()
    {
        return $this->belongsTo(Cat::class);
    }
}
