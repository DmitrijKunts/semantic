<?php

use App\Models\Cat;
use App\Models\Good;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('cats:reset', function () {
    Cat::query()->update(['feeded' => null]);
    $this->info('Cats reseted.');
})->purpose('Cats reset feeded time');

Artisan::command('goods:clear', function () {
    Good::truncate();
    Cat::query()->update(['feeded' => null]);
    $this->info('Goods cleared.');
})->purpose('Goods clear');
