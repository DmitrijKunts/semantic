<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Key extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['id', 'cat_id', 'name'];

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }

    public function snippets()
    {
        return $this->hasMany(Snippet::class);
    }

    public function youtubes()
    {
        return $this->hasMany(Youtube::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::title($value),
        );
    }
}
