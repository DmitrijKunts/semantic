<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Good extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku', 'code', 'name', 'link', 'slug', 'price', 'currency',
        'pictures', 'vendor', 'vendor_url', 'model',
        'meta_keys', 'meta_desc', 'desc', 'desc_plus', 'tech', 'equip'
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
        $first = collect($this->cats()->first()->goods()->reorder()->orderBy('id', 'desc')->where('goods.id', '<', $this->id)->limit(2)->get());
        $second = $this->cats()->first()->goods()->reorder()->orderBy('id', 'asc')->where('goods.id', '>', $this->id)->limit(2)->get();
        return $first->merge($second);
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
        return $cat != null && $cat->keysNotUsedWords->count() > 0
            ? Str::words($this->desc, 10, '') . ' ' . $cat->keysNotUsedWords->pop() . '...'
            : Str::words($this->desc, 10);
    }

    public static function makeFromXML($xml, Cat $cat)
    {
        $xmlObject = simplexml_load_string($xml);
        foreach ($xmlObject->offers->offer as $offer) {
            $good = Good::where('code', (string)$offer->id)->first();
            if (!$good) {
                $tech = [];
                foreach ($offer->tech_desc as $i) {
                    $tech[] = (string)$i;
                }
                $tech = implode(PHP_EOL, $tech);

                $equip = [];
                foreach ($offer->equip as $i) {
                    $equip[] = (string)$i;
                }
                $equip = implode(PHP_EOL, $equip);

                $pic = (string)$offer->picture;
                if ($offer->pictures) {
                    $pic = ($pic ? "$pic," : '') . (string)$offer->pictures;
                }

                $sku = (int)$offer->sku + 555;

                $good =  Good::Create([
                    'sku' => $sku,
                    'code' => (string)$offer->id,
                    'name' => (string)$offer->name,
                    'link' => (string)$offer->url,
                    'slug' => Str::of($sku . ' ' . $offer->name)->slug('-'),
                    'price' => (float)$offer->price,
                    'currency' => (string)$offer->currencyId,
                    'pictures' => $pic,
                    'vendor' => (string)$offer->vendor,
                    'vendor_url' => (string)$offer->vurl,
                    'model' => (string)$offer->model,
                    'meta_keys' => (string)$offer->meta_keys,
                    'meta_desc' => (string)$offer->meta_desc,
                    'desc' => (string)$offer->description,
                    'desc_plus' => (string)$offer->description_plus,
                    'tech' => $tech,
                    'equip' => $equip,
                ]);
            }
            $good->cats()->syncWithoutDetaching($cat);
            // dd($offer);
        }
    }
}
