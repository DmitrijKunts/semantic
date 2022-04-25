<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Good extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku', 'code', 'name', 'link', 'slug', 'price', 'currency',
        'pictures', 'vendor', 'vendor_url', 'model',
        'desc', 'summary', 'tech', 'equip'
    ];

    public function cats()
    {
        return $this->belongsToMany(Cat::class);
    }

    public function picture($index = 0)
    {
        return route('img', [$this->sku, $index]);
    }

    public function thumbnail($index = 0)
    {
        return route('img.small', [$this->sku, $index]);
    }

    public function equips()
    {
        return Str::of($this->equip)->explode(PHP_EOL)->all();
    }

    public function techs()
    {
        return Str::of($this->tech)->explode(PHP_EOL)->all();
    }

    public function techDiv($tech, $pos = 0)
    {
        return Str::of($tech)->explode(':')->all()[$pos] ?? '';
    }

    public function brothers()
    {
        $first = $this->cats()->first()->goods()->reorder()->orderBy('id', 'desc')->where('goods.id', '<', $this->id)->limit(3)->get();
        $second = $this->cats()->first()->goods()->reorder()->orderBy('id', 'asc')->where('goods.id', '>', $this->id)->limit(3)->get();
        return collect($first)->merge($second);
        // return $this->cats()->first()->goods()->where('goods.id', '<>', $this->id)->limit(3)->get();
    }

    public function nameKey($cat)
    {
        return $cat != null && $cat->keysNotUsedWords->count() > 0
            ? $this->name . ' ' . $cat->keysNotUsedWords->pop()
            : $this->name;
    }

    public function descKey($cat)
    {
        if ($this->summary != '') {
            $text = $this->summary;
        } else {
            $text = $this->desc;
        }
        return $cat != null && $cat->keysNotUsedWords->count() > 0
            ? Str::words($text, 30, '') . ' ' . $cat->keysNotUsedWords->pop() . '...'
            : Str::words($text, 30);
    }

    public static function makeFromJson($jsonString, Cat $cat)
    {
        $json = json_decode($jsonString);
        if (!$json->success) {
            abort(500, $json->message);
        }
        foreach ($json->offers as $offer) {
            $good = Good::where('code', (string)$offer->code)->first();
            if (!$good) {
                $tech = [];
                foreach ($offer->tech_desc ?? [] as $i) {
                    $tech[] = (string)$i;
                }
                $tech = implode(PHP_EOL, $tech);

                $equip = [];
                foreach ($offer->equip ?? [] as $i) {
                    $equip[] = (string)$i;
                }
                $equip = implode(PHP_EOL, $equip);

                $sku = genConst(9999999, $offer->code);

                $good =  Good::Create([
                    'sku' => $sku,
                    'code' => (string)$offer->code,
                    'name' => (string)$offer->name,
                    'link' => (string)$offer->url,
                    'slug' => Str::of($sku . ' ' . $offer->name)->slug('-'),
                    'price' => (float)$offer->price,
                    'currency' => (string)$offer->currencyId,
                    'pictures' => (string)$offer->pictures,
                    'vendor' => (string)$offer->vendor,
                    'vendor_url' => (string)($offer->vurl ?? ''),
                    'model' => (string)$offer->model,
                    'desc' => (string)$offer->description,
                    'summary' => (string)($offer->summary ?? ''),
                    'tech' => $tech,
                    'equip' => $equip,
                ]);
            }
            $good->cats()->syncWithoutDetaching($cat);
            DB::table('cat_good')
                ->where('cat_id', $cat->id)
                ->where('good_id', $good->id)
                ->update(['rank' => (string)$offer->rank]);
        }
    }
}
