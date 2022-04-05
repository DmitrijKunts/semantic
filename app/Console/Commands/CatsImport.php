<?php

namespace App\Console\Commands;

// use App\Imports\CatsImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class CatsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cats:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import cats with semantic from Excel-file';

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
        $this->output->title('Starting import');
        Excel::import(new \App\Imports\CatsImport($this), storage_path('cats.xlsx'));
        Cache::flush();
        $this->output->success('Import successful');
        return 0;
    }
}
