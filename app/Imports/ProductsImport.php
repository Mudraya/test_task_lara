<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class ProductsImport implements
    OnEachRow,
    SkipsOnError,
    SkipsOnFailure,
    WithChunkReading,
    WithValidation

{
    use Importable, SkipsErrors, SkipsFailures;

    private $successfulRows = 0;

    public function onRow(Row $row)
    {
        // app doesn't load first row(head)
        if ($row->getIndex() != 1) {
            $row      = $row->toArray();

            ini_set('max_execution_time', 10);

            $this->successfulRows++;

            $parent_id = null;

            // '$i = ($row[0] == '') ? 1 : 0' - for rows where first cell is empty
            for($i = ($row[0] == '') ? 1 : 0; $i<3; $i++) {
                $category = Category::firstOrCreate([
                    'name' => $row[$i],
                    'parent_id' => $parent_id,
                ]);
                $parent_id = $category->id;
            }

            Product::create([
                'category_id' => $parent_id,
                'producer' => $row[3],
                'name' => $row[4],
                'code' => $row[5],
                'description' => $row[6],
                'retail_price' => $row[7],
                'guarantee' => ($row[8] == 'Нет') ? 0 : $row[8],
                'is_available' => ($row[9] == 'есть в наличие') ? 1 : 0
            ]);
        }

    }



    public function rules(): array
    {
        return [
            '*.1' => 'required',
            '*.2' => 'required',
            '*.3' => 'required',
            '*.4' => 'required',
            '*.5' => 'required|unique:products,code',
            '*.6' => 'required',
            '*.7' => 'required|numeric',
            '*.8' => array('required', 'regex:/Нет|\d+/'),
            '*.9' => 'required',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            '1' => 'rubric',
            '2' => 'category',
            '3' => 'producer',
            '4' => 'name',
            '5' => 'code',
            '6' => 'description',
            '7' => 'retail price',
            '8' => 'guarantee',
            '9' => 'availability',
        ];
    }

    public function chunkSize(): int
    {
        return 300;
    }

    public function getSuccessfulRowCount(): int
    {
        return $this->successfulRows;
    }

}
