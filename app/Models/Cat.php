<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Cat extends Model
{
    use HasFactory;

    public $keysNotUsedWords = null;

    public $timestamps = false;

    protected $fillable = ['id', 'p_id', 'name', 'slug', 'text', 'sheet'];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => config('app.locale') == 'en' ? Str::title($value) : $value,
        );
    }

    public function canonical()
    {
        if ($this->goods->count() || $this->childs->count() || !$this->parent) {
            return route('cat', $this);
        } else {
            return route('cat', $this->parent);
        }
    }

    public function scopeActive($query)
    {
        $query->withCount('goods')
            ->withCount('keys')
            ->addSelect(DB::raw('(SELECT count(*) FROM cats as c WHERE c.p_id=cats.id) childs_count'));

        if (getBanner()) {
            $query
                ->where(function ($query) {
                    $query->whereRaw('(SELECT count(*) FROM cats as c WHERE c.p_id=cats.id)>0')
                        ->orWhereRaw('(select count(*) from "keys" where "cats"."id" = "keys"."cat_id")>0');
                });
        } else {
            $query
                ->where(function ($query) {
                    $query
                        // ->where('childs_count', '>', 0)
                        ->whereRaw('(SELECT count(*) FROM cats as c WHERE c.p_id=cats.id)>0')

                        ->orWhere(function ($query) {
                            $query
                                // ->where('keys_count', '>', 0)
                                ->whereRaw('(select count(*) from "keys" where "cats"."id" = "keys"."cat_id")>0')
                                ->where(function ($query) {
                                    $query
                                        // ->where('goods_count', '>', 0)
                                        ->whereRaw('(select count(*) from "goods" inner join "cat_good" on "goods"."id" = "cat_good"."good_id" where "cats"."id" = "cat_good"."cat_id")>0')

                                        ->orWhereNull('feeded');
                                });
                        });
                });
        }
        return $query;
    }

    public function childs()
    {
        return $this->hasMany(Cat::class, 'p_id')->active();
    }

    public function keys()
    {
        return $this->hasMany(Key::class);
    }

    public function calcKeysNotUsedWords()
    {
        $nameWords = Str::of($this->name)
            ->lower()
            ->split('~\s+~')
            ->map(fn ($i) => Str::lower(trim($i)))
            ->filter(function ($i) {
                if ($i == '') return false;
                if (Str::of($i)->match('~^[??-??]{1,3}$~iu') != '') return false;
                return true;
            })->unique();

        $words = '';
        foreach ($this->keys as $key) {
            $words .= " {$key->name}";
        }
        $words = Str::of($words)
            ->split('~\s+~')
            ->map(fn ($i) => Str::lower(trim($i)))
            ->filter(function ($i) {
                if ($i == '') return false;
                if (Str::of($i)->match('~^[??-??]{1,3}$~iu') != '') return false;
                return true;
            })
            ->unique()
            ->reverse()
            ->diff($nameWords);
        $this->keysNotUsedWords = $words;
    }

    public function key($shift = '')
    {
        return constOne($this->keys()->get()->all(), $shift)->name ?? $this->name;
    }

    public function parent()
    {
        return $this->belongsTo(Cat::class, 'p_id');
    }

    public function brothers()
    {
        return $this->parent->childs()
            ->where('id', '<>', $this->id);
    }

    public function goods()
    {
        return $this->belongsToMany(Good::class)
            ->withPivot('rank')
            ->orderByRaw(config('feed.goods_order'));
    }

    public function snippet2Text()
    {
        return Cache::rememberForever('snippet_' . $this->id, function () {
            $res = collect([]);
            $keys = collect(constSort($this->keys, 'keys_snippets' . $this->name))->slice(0, 10);
            foreach ($keys  as $key) {
                $_snippets = [];
                foreach ($key->snippets as $snippet) {
                    $_snippets[] = $snippet->snippet;
                }
                $res = $res->merge(collect(constSort($_snippets, 'snippets' . $key->name))->slice(0, 3));
            }
            return $res->unique();
        });
    }

    public function youtubes()
    {
        return $this->hasManyThrough(Youtube::class, Key::class);
    }

    public function youtubesUniq()
    {
        return collect(constSort($this->youtubes->unique('url'), 'youtubesUniq' . request()->getRequestUri()))->slice(0, 5);
    }
}
