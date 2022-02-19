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
    public function sheets(): array
    {
        $res = [];
        foreach (range(1, 1000) as $k => $v) {
            $res[$k] = new ClusterSheetImport();
        }
        $res[0] = new MapSheetImport();
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
            $_c = Cat::firstOrCreate([
                'p_id' => self::$levels[$lastCol - 1],
                'name' => $_name,
                'slug' => Str::of($_name)->slug('-')->start('/')//->finish('/'),
            ]);

            self::$levels[$lastCol] = $_c->id;
        }
    }
}

class ClusterSheetImport implements OnEachRow
{
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
