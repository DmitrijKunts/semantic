<?php

namespace App\Http\Controllers;

use App\Models\Good;
use Illuminate\Http\Request;

class GoodController extends Controller
{
    public function index(Good $good)
    {
        return view('good', compact('good'));
    }
}
