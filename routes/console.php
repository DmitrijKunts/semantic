<?php

use App\Http\Controllers\CatController;
use App\Models\Cat;
use App\Models\Good;
use App\Models\Snippet;
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

Artisan::command('cats:crawl', function () {
    $cc = new CatController;
    $this->withProgressBar(Cat::all(), function ($cat) use ($cc) {
        $cc->index($cat);
    });
    $this->info("\nCats crawled.");
})->purpose('Cats crawl every node.');



Artisan::command('keys:snippet', function () {
    $this->output->title('Starting import snippets to keys');
    Snippet::query()->truncate();
    Excel::import(new \App\Imports\SnippetImport, storage_path('serp.xlsx'));
    $this->output->success('Import successful');
})->purpose('Import snippets to keys');


Artisan::command('goods:clear', function () {
    Good::truncate();
    Cat::query()->update(['feeded' => null]);
    $this->info('Goods cleared.');
})->purpose('Goods clear');


Artisan::command('make', function () {
    $this->call('migrate:fresh', ['--force' => 1]);
    $this->call('cats:import');
    $this->call('keys:snippet');
    $this->call('cats:crawl');
})->purpose('Make all');
