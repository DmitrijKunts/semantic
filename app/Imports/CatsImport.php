<?php

namespace App\Imports;

use App\Models\Cat;
use App\Models\Key;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class CatsImport implements WithMultipleSheets, SkipsUnknownSheets
{
    private $output;

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function sheets(): array
    {
        $res = [];
        foreach (range(1, 1000) as $k => $v) {
            $res[$k] = new ClusterSheetImport($this->output);
        }
        $res[0] = new MapSheetImport($this->output);
        return $res;
    }

    public function onUnknownSheet($sheetName)
    {
        // E.g. you can log that a sheet was not found.
        // info("Sheet {$sheetName} was skipped");
    }
}

class MapSheetImport implements OnEachRow
{
    static private $levels = [1 => -1, 0 => -1];
    private $output;

    public function __construct($output)
    {
        $this->output = $output;
        $this->output->info("Cats importing...");
    }

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        $lastCol = -1;
        foreach ($row as $key => $col) {
            if ($key == 0) {
                continue;
            }
            if ($col != '') {
                $lastCol = $key;
                $_name = $col;
            }
        }
        if ($_name != '') {
            $pId = self::$levels[$lastCol - 1];
            $_c = Cat::firstOrCreate([
                'p_id' => $pId,
                'name' => $_name,
                'slug' => self::genSlug($_name, $pId)
            ]);

            self::$levels[$lastCol] = $_c->id;
        }
    }

    public static function genSlug($name, $pId)
    {
        $slug = Str::of($name)->slug('-');
        if (Cat::where('slug', $slug)->exists()) {
            return Str::of("$name $pId")->slug('-');
        }
        return $slug;
    }
}

class ClusterSheetImport implements OnEachRow
{
    private $output;

    public function __construct($output)
    {
        $this->output = $output;
        $this->output->info("$sheet importing...");
    }

    public function onRow(Row $row)
    {
        if (($sheet = $row->getDelegate()->getWorksheet()->getTitle()) == 'Справка') return;
        if ($row->getIndex() < 3) return;
        if (Str::of($sheet)->endsWith('...')) {
            $cat = Cat::where('name', 'like', Str::of($sheet)->replace('...', '%'))->first();
        } else {
            $cat = Cat::where('name', $sheet)->first();
        }

        if (!$cat) return;
        $row = $row->toArray();
        Key::firstOrCreate(['cat_id' => $cat->id, 'name' => $row[0]]);
    }
}
