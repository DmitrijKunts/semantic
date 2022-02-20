<?php

namespace App\Http\Controllers;

use App\Feed;
use App\Models\Cat;
use App\Models\Good;
use Illuminate\Http\Request;

class CatController extends Controller
{
    public function front()
    {
        return view('front');
    }

    public function index(Cat $cat)
    {
        if ($cat->childs->count() == 0 && $cat->feeded == null) {
            Good::makeFromXML(Feed::getFeed($cat->name), $cat);
            $cat->feeded = now();
            $cat->save();
        }
        return view('cat', compact('cat'));
    }
}
