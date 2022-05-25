<?php

namespace App\Http\Controllers;

use App\Models\Good;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GoodController extends Controller
{
    private function genConst($val, $domain = '')
    {
        return $val == 0 ? 0 : hexdec(substr(sha1($domain), 0, 15)) % $val;
    }

    public function index(Good $good)
    {
        if (config('app.redirect_to') != '') {
            $sku = $this->genConst(9999999, config('app.redirect_to') . $good->code);
            $url = 'https://' . config('app.redirect_to') .
                Str::replace(
                    "/{$good->sku}",
                    "/$sku",
                    route('good', $good, false)
                );
            return redirect($url, 301);
        }
        $key = 'redirected:ip=' . request()->ip();
        if (!app()->runningUnitTests() && !isBot() && !Cache::store('fileshared')->has($key)) {
            Cache::store('fileshared')->put($key, true, 60 * 60 * 24);
            return redirect(BuyController::teleport($good->link, 'buy', route('good', $good)));
        }

        return view('good', compact('good'));
    }
}
