<?php

namespace App\Imports;

use App\Models\Cat;
use App\Models\Key;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class KeysImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        if ($row->getIndex() < 2) return;
        $row = $row->toArray();
        $cat = Cat::firstWhere('name', $row[1]);

        if (!$cat) return;
        Key::firstOrCreate(['cat_id' => $cat->id, 'name' => $row[0]]);
    }


}

