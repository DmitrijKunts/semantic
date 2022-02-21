<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use App\Models\Good;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $cats = Cat::withCount('goods')
            ->where('goods_count', '>', 0)
            ->orWhere(function ($query) {
                $query->selectRaw('count(*)')
                    ->from('cats as c')
                    ->whereColumn('c.p_id', 'cats.id');
            }, '>', 0)
            ->get();
        $goods = Good::all();
        return response()
            ->view('sitemap', compact('cats', 'goods'))
            ->header('Content-Type', 'text/xml');
    }
}
