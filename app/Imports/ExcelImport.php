<?php

namespace App\Imports;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
class ExcelImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Clientes|null
     */
    public function model(array $row)
    {
        return $row;
        
    }


}
