<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use Illuminate\Http\Request;

class CatController extends Controller
{
    public function front(){
        $menu = Cat::where('p_id', -1)->get();
        return view('front', compact('menu'));
    }

    public function index(Request $request){
        $cat = Cat::where('slug', $request->getRequestUri())->firstOrFail();
        $menu = Cat::where('p_id', -1)->get();
        return view('cat', compact('menu', 'cat'));
    }
}
