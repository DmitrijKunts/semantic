<?php

use App\Http\Controllers\CatController;
use App\Models\Cat;
use App\Models\Good;
use App\Models\Key;
use App\Models\Snippet;
use App\Models\Youtube;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

Artisan::command('domains:down', function () {
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
})->purpose('All domains down.');

Artisan::command('domains:up', function () {
    $this->call('up', [], $this->output);
    foreach (config('domain.domains') as $domain) {
        File::delete(storage_path(domain_sanitized($domain) . '/framework/maintenance.php'));
        File::delete(storage_path(domain_sanitized($domain) . '/framework/down'));
    }
})->purpose('All domains up.');


Artisan::command('cats:reset', function () {
    Cat::query()->update(['feeded' => null]);
    $this->info('Cats reseted.');
})->purpose('Cats reset feeded time');

Artisan::command('cats:crawl', function () {
    Cat::query()->update(['feeded' => null]);
    $cc = new CatController;
    $this->withProgressBar(Cat::withCount('keys')->where('keys_count', '>', 0)->get(), function ($cat) use ($cc) {
        $cc->index($cat, true);
    });
    $this->info("\nCats crawled.");
    $this->call('stat');
})->purpose('Cats crawl every node.');

Artisan::command('cats:refeed', function () {
    if (app()->domain() == '') {
        $appPath = base_path();
        foreach (config('domain.domains') as $d) {
            $this->info($d);
            print shell_exec(sprintf('cd %s && php artisan cats:refeed --domain=%s', $appPath, $d));
        }
    } else {
        $cats = Cat::withCount('keys')
            ->where('keys_count', '>', 0)
            ->where(function ($query) {
                // $query->where('feeded', '<', now()->addMinutes(-2))
                $query->where('feeded', '<', now()->addDays(-config('feed.update_every_days', 10)))
                    ->orWhereNull('feeded');
            })
            ->get();
        if ($cats->count()) {
            $cc = new CatController;
            $this->withProgressBar($cats, function ($cat) use ($cc) {
                $cc->index($cat, true);
            });
            $this->info("\nCats crawled.");
        }
    }
})->purpose('Update goods from feed server for every category.');


Artisan::command('keys:clear', function () {
    Key::truncate();
    $this->output->success('Key truncated!');
})->purpose('Truncate keys');

Artisan::command('keys:import', function () {
    $this->output->title('Starting import keys');
    if (!File::exists(storage_path('keys.xlsx'))) {
        $this->output->title(storage_path('keys.xlsx') . ' - file not found, exit');
        return;
    }
    Snippet::query()->truncate();
    Excel::import(new \App\Imports\KeysImport, storage_path('keys.xlsx'));
    Cache::flush();
    $this->output->success('Import successful');
})->purpose('Import keys');

Artisan::command('keys:snippet', function () {
    $this->output->title('Starting import snippets to keys');
    Excel::import(new \App\Imports\SnippetImport, storage_path('serp.xlsx'));
    Cache::flush();
    $this->output->success('Import successful');
})->purpose('Import snippets to keys');

Artisan::command('snippets:clear', function () {
    Snippet::query()->truncate();
    Cache::flush();
    $this->output->success('Snippets cleared.');
})->purpose('Clear snippets.');

Artisan::command('youtubes:clear', function () {
    Youtube::query()->truncate();
    Cache::flush();
    $this->output->success('Youtubes cleared.');
})->purpose('Clear youtubes.');


Artisan::command('goods:clear', function () {
    Good::truncate();
    Cat::query()->update(['feeded' => null]);
    $this->info('Goods cleared.');
})->purpose('Goods clear');



Artisan::command('make', function () {
    $this->call('migrate:fresh', ['--force' => 1]);
    $this->call('cats:import');
    $this->call('keys:import');
    $this->call('keys:snippet');
    $this->call('cats:crawl');
})->purpose('Make all');


Artisan::command('stat', function () {
    $this->table(
        ['Object', 'Value'],
        [
            ['Categories [with keys]', Cat::all()->count() . ' [' . Cat::withCount('keys')->where('keys_count', '>', 0)->get()->count() . ']'],
            ['Keys', Key::all()->count()],
            ['Goods', Good::all()->count()],
            ['Total', Cat::all()->count() + Key::all()->count() + Good::all()->count()],
        ]
    );
})->purpose('Show statistic');
