<?php

namespace App\Http\Controllers;

use App\Feed;
use App\Models\Cat;
use App\Models\Good;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CatController extends Controller
{
    public function front()
    {
        return view('front');
    }

    public function index(Cat $cat, $crawl = false)
    {
        if ('temako-suchi.ru' == app()->domain()) dd(request());
        $catChilds = null;
        if ($cat->childs->count() == 0 && $cat->feeded == null) {
            Good::makeFromJson(Feed::getFeed($cat->name), $cat);
            $cat->feeded = now();
            $cat->save();
        } else {
            if (getBanner()) {
                $catChilds = $cat->childs()->paginate(30, pageName: 'cat_page');
            } else {
                $catChilds = Cat::withCount('goods')
                    ->where('p_id',  $cat->id)
                    ->where(function ($query) use ($cat) {
                        $query->where('feeded', null)
                            // ->orWhere('goods_count', '>', 0)//т.к. не работет с пагинацией
                            ->orWhereRaw('(select count(*) from "goods" inner join "cat_good" on "goods"."id" = "cat_good"."good_id" where "cats"."id" = "cat_good"."cat_id")>0') //т.к. не работет с пагинацией
                            ->orWhere(function ($query) {
                                $query->selectRaw('count(*)')
                                    ->from('cats as c')
                                    ->whereColumn('c.p_id', 'cats.id');
                            }, '>', 0);
                    })
                    ->paginate(30, pageName: 'cat_page');
            }
        }
        if ($crawl) return;

        return view('cat', compact('cat', 'catChilds'));
    }
}
