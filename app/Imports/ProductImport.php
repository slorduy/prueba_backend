<?php

namespace App\Imports;

use App\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ProductImport implements ToModel , WithHeadingRow ,WithCustomCsvSettings
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
            return new Product([
                'name'     => $row['name'],
                'description'    => $row['description'], 
                'price' => $row['price'],
             ]);

             
        
    }

    public function getCsvSettings(): array
             {
                return [
                    'delimiter' => ";"
                ];
             }
}
