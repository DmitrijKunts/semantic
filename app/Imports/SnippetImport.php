<?php

namespace App\Imports;

use App\Models\Key;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class SnippetImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        $res = [];
        foreach (range(1, 10000) as $k => $v) {
            $res[$k] = new SerpSheetImport;
        }
        return $res;
    }

    public function onUnknownSheet($sheetName)
    {
    }
}

class SerpSheetImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        if (($sheet = $row->getDelegate()->getWorksheet()->getTitle()) == 'Карта') return;
        if ($row->getIndex() < 3) return;
        if ($row[0] == '' || $row[5] == '') return;
        $key = Key::firstWhere('name', $row[0]);
        if (!$key) return;
        $key->snippets()->create(['snippet'=>$row[5]]);
    }
}
