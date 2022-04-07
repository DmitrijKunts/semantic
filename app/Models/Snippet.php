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
            'Magazilla' => '',
            'Touch' => '',
            'e-Katalog' => '',
            '\.\.\.' => '',
            '\(\d{3}\) \d \d{3} \d{3}' => '',
            '\(\d{3}\) \d{3}-\d{2}-\d{2}' => '',
            '\(\d{3}\)\d{3}-\d{2}-\d{2}' => '',
            '\\+d{3} (\d{2}\) \d{3}-\d{2}-\d{2}' => '',
            '\d-\d{3}-\d{3}-\d{3}' => '',
            '\d \d{3} \d{3}' => '',
        ];
        if (config('app.locale') == 'en') {
            $filters['[а-я]'] = '';
        }
        if (config('feed.geo') == 'ru') {
            $filters['Украине'] = '';
            $filters['грн\.'] = '';
            $filters['₴'] = '';
        }

        $str = Str::of($str);
        foreach ($filters as $p => $r) {
            $str = $str->replaceMatches(Str::of($p)->start('~')->finish('~ui'), $r);
        }
        return  $str;
    }
}
