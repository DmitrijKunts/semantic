<?php

namespace App\Console\Commands;

use App\Models\Good;
use Illuminate\Console\Command;

class GoodsClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goods:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Goods clear';

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
        Good::truncate();
        return 0;
    }
}
