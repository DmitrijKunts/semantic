<?php

namespace App\Console\Commands;

use App\Models\Cat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CatsTextUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cats:textupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cats text update';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = 0;
        $this->withProgressBar(Cat::all(), function ($cat) use (&$count) {
            $textFile = storage_path("texts/{$cat->name}.txt");
            if (File::exists($textFile)) {
                $cat->text = File::get($textFile);
                $cat->save();
                $count++;
            }
        });
        $this->newLine();
        $this->info("$count cats updated.");
        return 0;
    }
}
