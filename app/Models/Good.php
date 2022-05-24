<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Good extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku', 'code', 'name', 'link', 'slug', 'price', 'oldprice', 'currency',
        'pictures', 'alts', 'vendor', 'model',
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

    public function pictures()
    {
        return Str::of($this->pictures)->explode(',');
    }

    public function alts()
    {
        return Str::of($this->alts)->explode(',');
    }

    public function alt($index = 0)
    {
        return $this->alts()[$index];
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
        $sents = Str::of($text)
            ->explode('.')
            ->filter(fn ($i) => $i != '')
            ->map(fn ($i) => trim($i))
            ->slice(0, 2);
        $text = constSort($sents, $cat->id ?? '')->join('. ');

        return $cat != null && $cat->keysNotUsedWords->count() > 0
            ? Str::words($text, 30, '') . ' ' . $cat->keysNotUsedWords->pop() . '...'
            : Str::words($text, 30);
    }

    /**
     * @param mixed $jsonString
     * @param Cat $cat
     *
     * @return [type]
     */
    public static function makeFromJson($jsonString, Cat $cat)
    {
        if ($jsonString == null) return false;
        $json = json_decode($jsonString);
        if (!$json->success) {
            abort(500, $json->message);
            return false;
        }

        DB::beginTransaction();
        foreach ($json->offers as $offer) {
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

            $good = Good::where([
                ['code', (string)$offer->code],
                ['updated_at', '>=', now()->addHours(-2)],
            ])->first();
            if (!$good) {
                $good =  Good::updateOrCreate(
                    ['code' => (string)$offer->code],
                    [
                        'sku' => $sku,
                        'name' => (string)$offer->name,
                        'link' => (string)$offer->url,
                        'slug' => Str::of($sku . ' ' . $offer->name)->slug('-'),
                        'price' => (float)$offer->price,
                        'oldprice' => (float)$offer->oldprice,
                        'currency' => (string)$offer->currencyId,
                        'pictures' => implode(',', Arr::wrap($offer->pictures)),
                        'alts' => implode(',', Arr::wrap($offer->alts)),
                        'vendor' => (string)$offer->vendor,
                        'model' => (string)$offer->model,
                        'desc' => (string)$offer->description,
                        'summary' => (string)($offer->summary ?? ''),
                        'tech' => $tech,
                        'equip' => $equip,
                    ]
                );
            }

            $good->cats()->syncWithoutDetaching($cat);
            DB::table('cat_good')
                ->where('cat_id', $cat->id)
                ->where('good_id', $good->id)
                ->update(['rank' => (string)$offer->rank]);
        }
        DB::commit();
        return true;
    }
}
