<?php

namespace App\Console\Commands;

use App\Models\Cat;
use App\Models\Good;
use Illuminate\Console\Command;

class CatsNorm extends Command
{
    private int $minGoodsInGroup = 5;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cats:norm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize offers for categories';

    private function normalize(Cat $cat)
    {
        $goods = $cat->goods()->get();
        foreach ($goods as $good) {
            $detachs = [];
            foreach ($good->cats()->where('cats.id', '<>', $cat->id)->withCount('goods')->get() as $c) {
                if ($c->goods_count <= $this->minGoodsInGroup) {
                    continue;
                }
                $detachs[] = $c->id;
            }
            if (count($detachs) <= $this->minGoodsInGroup) {
                continue;
            }

            if (count($detachs) > 0) {
                $good->cats()->detach($detachs);
            }
        }
    }

    public function handle()
    {
        if (config('app.redirect_to') != '') {
            $this->info("Use redirect. Skipping.");
        }
        $cats = Cat::withCount('goods')->orderBy('goods_count')->get();
        $this->withProgressBar($cats, function ($cat) {
            $this->normalize($cat);
        });
        $this->info("\nCats normilzed!");
        return 0;
    }
}
