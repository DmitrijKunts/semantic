<?php

use App\Http\Controllers\CatController;
use App\Models\Cat;
use App\Models\Good;
use App\Models\Snippet;
use Illuminate\Support\Facades\Artisan;

Artisan::command('domain:down', function () {
    $this->call('down', [], $this->output);
    foreach (config('domain.domains') as $domain) {
        File::copy(
            storage_path('framework/maintenance.php'),
            storage_path(domain_sanitized($domain) . '/framework/maintenance.php')
        );
        File::copy(
            storage_path('framework/down'),
            storage_path(domain_sanitized($domain) . '/framework/down')
        );
    }
})->purpose('All domain down.');

Artisan::command('domain:up', function () {
    $this->call('up', [], $this->output);
    foreach (config('domain.domains') as $domain) {
        File::delete(storage_path(domain_sanitized($domain) . '/framework/maintenance.php'));
        File::delete(storage_path(domain_sanitized($domain) . '/framework/down'));
    }
})->purpose('All domain up.');


Artisan::command('cats:reset', function () {
    Cat::query()->update(['feeded' => null]);
    $this->info('Cats reseted.');
})->purpose('Cats reset feeded time');

Artisan::command('cats:crawl', function () {
    Cat::query()->update(['feeded' => null]);
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
