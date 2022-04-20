<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use App\Models\Good;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $cats = Cat::filter(Cat::query())->get();
        $goods = Good::all();
        return response()
            ->view('sitemap', compact('cats', 'goods'))
            ->header('Content-Type', 'text/xml');
    }
}
