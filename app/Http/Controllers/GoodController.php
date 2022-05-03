<?php

namespace App\Http\Controllers;

use App\Models\Good;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GoodController extends Controller
{


    public function index(Good $good)
    {
        $key = 'redirected:ip=' . request()->ip();
        if (!isBot() && !Cache::store('fileshared')->has($key)) {
            Cache::store('fileshared')->put($key, true, 60 * 60 * 24);
            return redirect(BuyController::teleport($good->link, 'buy'));
        }

        return view('good', compact('good'));
    }
}
