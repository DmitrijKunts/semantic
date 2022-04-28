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
        if (Cat::filter($cat->where('id', $cat->id))->count() == 0) abort(404);
        $catChilds = null;
        if ($cat->childs->count() == 0 && ($cat->feeded == null || $crawl)) {
            if (Good::makeFromJson(Feed::getFeed($cat->name), $cat)) {
                $cat->feeded = now();
                $cat->save();
            }
        } else {
            $catChilds = $cat->childs()->paginate(30, pageName: 'cat_page');
        }
        if ($crawl) return;

        return view('cat', compact('cat', 'catChilds'));
    }
}
