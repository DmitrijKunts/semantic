<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Youtube extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['id', 'key_id', 'url', 'title', 'snippet'];

    public function key()
    {
        return $this->belongsTo(Key::class);
    }

    protected function snippet(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => snippetClear($value),
        );
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => snippetClear($value),
        );
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::replace('/watch?v=', '/embed/', $value),
        );
    }
}
