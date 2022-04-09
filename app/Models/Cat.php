<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Cat extends Model
{
    use HasFactory;

    public $keysNotUsedWords = null;

    public $timestamps = false;

    protected $fillable = ['id', 'p_id', 'name', 'slug', 'text', 'sheet'];

    public function canonical()
    {
        if ($this->goods->count() || $this->childs->count()) {
            return route('cat', $this);
        } else {
            return route('cat', $this->parent);
        }
    }

    public function childsNotBlank()
    {
        if (getBanner()) {
            return $this->childs();
        } else {
            return Cat::withCount('goods')
                ->where('p_id',  $this->id)
                ->where(function ($query) {
                    $query->where('feeded', null)
                        ->orWhere('goods_count', '>', 0)
                        ->orWhere(function ($query) {
                            $query->selectRaw('count(*)')
                                ->from('cats as c')
                                ->whereColumn('c.p_id', 'cats.id');
                        }, '>', 0);
                });
        }
    }

    public function childs()
    {
        return $this->hasMany(Cat::class, 'p_id');
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
            ->map(fn ($i) => trim($i))
            ->filter(function ($i) {
                if ($i == '') return false;
                if (Str::of($i)->match('~^[а-я]{1,3}$~iu') != '') return false;
                return true;
            })->unique();

        $words = '';
        foreach ($this->keys as $key) {
            $words .= " {$key->name}";
        }
        $words = Str::of($words)
            ->split('~\s+~')
            ->map(fn ($i) => trim($i))
            ->filter(function ($i) {
                if ($i == '') return false;
                if (Str::of($i)->match('~^[а-я]{1,3}$~iu') != '') return false;
                return true;
            })
            ->unique()
            ->reverse()
            ->diff($nameWords);
        // ->implode(',');
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
        return Cat::where('p_id', $this->p_id)->where('id', '<>', $this->id)->get();
    }

    public function brothersNotBlank()
    {
        return Cat::withCount('goods')
            ->where('id', '<>', $this->id)
            ->where('p_id',  $this->p_id)
            ->where(function ($query) {
                $query->where('feeded', null)
                    ->orWhere('goods_count', '>', 0)
                    ->orWhere(function ($query) {
                        $query->selectRaw('count(*)')
                            ->from('cats as c')
                            ->whereColumn('c.p_id', 'cats.id');
                    }, '>', 0);
            })
            ->limit(20)
            ->get();
        // return Cat::where('p_id', $this->p_id)->where('id', '<>', $this->id)->limit(20)->get();
    }

    public function goods()
    {
        return $this->belongsToMany(Good::class)->orderByRaw(config('feed.goods_order'));
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
}
