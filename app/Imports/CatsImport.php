<?php

namespace App\Imports;

use App\Models\Cat;
use App\Models\Key;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class CatsImport implements WithMultipleSheets, SkipsUnknownSheets
{
    private $output;
    private $importKeys;

    public function __construct($output, $importKeys = true)
    {
        $this->output = $output;
        $this->importKeys = $importKeys;
    }

    public function sheets(): array
    {
        $res = [];
        if ($this->importKeys) {
            foreach (range(1, 10000) as $k => $v) {
                $res[$k] = new ClusterSheetImport($this->output);
            }
        }
        $res[0] = new MapSheetImport($this->output);
        return $res;
    }

    public function onUnknownSheet($sheetName)
    {
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

    public function extractName($cell)
    {
        $cell = Str::of($cell);
        $name = (string)$cell->match('~, "(.+?)"\)~u');
        $sheet = (string)$cell->match('~"#"&"\'(.+?)\'!~u');
        if ($name == '') {
            $name = (string)$cell;
            $sheet = (string)$cell;
        }
        return [$name, $sheet];
    }

    public function onRow(Row $row)
    {
        $_row = $row->toArray();
        $lastCol = -1;
        $_name = '';
        $_sheet = '';
        foreach ($_row as $key => $col) {
            if ($key == 0) {
                continue;
            }
            if ($col != '') {
                $lastCol = $key;
                list($_name, $_sheet) = $this->extractName($col);
            }
        }
        if ($_name != '') {
            $text = '';
            $textFile = storage_path("texts/$_name.txt");
            if (File::exists($textFile)) {
                $text = File::get($textFile);
            }
            $pId = self::$levels[$lastCol - 1];
            try {
                $_c = Cat::updateOrCreate(
                    [
                        'name' => $_name
                    ],
                    [
                        'p_id' => $pId,
                        'slug' => self::genSlug($_name, $pId),
                        'sheet' => $_sheet,
                        'text' => $text,
                    ]
                );
                self::$levels[$lastCol] = $_c->id;
            } catch (\Exception $e) {
                $this->output->info($e->getMessage());
            }
        }
    }

    public static function genSlug($name, $pId)
    {
        $suffix = '';
        while (true) {
            $slug = Str::of("$name$suffix")->slug('-');
            if (Cat::where('slug', $slug)->where('name', '<>', $name)->exists()) {
                $suffix++;
                continue;
            }
            return $slug;
        }
    }
}

class ClusterSheetImport implements OnEachRow
{
    private $output;

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function onRow(Row $row)
    {
        if (($sheet = $row->getDelegate()->getWorksheet()->getTitle()) == 'Справка') return;
        if ($row->getIndex() < 3) return;
        $cat = Cat::firstWhere('sheet', $sheet);

        if (!$cat) return;
        $row = $row->toArray();
        Key::firstOrCreate(['cat_id' => $cat->id, 'name' => $row[0]]);
    }
}
