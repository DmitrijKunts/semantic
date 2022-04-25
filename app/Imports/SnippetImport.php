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
        $row = $row->toArray();
        if ($row[0] == '') return;
        $key = Key::firstWhere('name', $row[0]);
        if (!$key) return;
        if ($row[5] != '') {
            $key->snippets()->updateOrCreate(['snippet' => $row[5]]);
        }
        if ($row[7] != '' && $row[8] != '' && $row[9] != '') {
            $key->youtubes()->updateOrCreate(
                ['url' => $row[7]],
                ['title' => $row[8], 'snippet' => $row[9],]
            );
        }
    }
}
