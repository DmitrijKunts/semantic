<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Snippet extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['id', 'key_id', 'snippet'];

    public function key()
    {
        return $this->belongsTo(Key::class);
    }

    protected function snippet(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => self::clean($value),
        );
    }

    private static function clean($str): string
    {
        $filters = [
            'HOTLINE' => '',
            'R0ZЕТKА_OLD' => '',
            'ROZETKA' => '',
            'CACTUS' => '',
            'ELDORADO' => '',
            'Эпицентр' => '',
            'Фокстрот' => '',
            '[a-z0-9]+\.[a-z]+\.[a-z]+' => '',
            '[a-z0-9]+\.[a-z]+' => '',
            '\d-\d{3}-\d{3}-\d{3}' => '',
            'Magazilla' => '',
            'Touch' => '',
            'e-Katalog' => '',
        ];

        $str = Str::of($str);
        foreach ($filters as $p => $r) {
            $str = $str->replaceMatches(Str::of($p)->start('~')->finish('~ui'), $r);
        }
        return  $str;
    }
}
