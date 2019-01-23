<?php

use Illuminate\Database\Seeder;
use App\Position;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //парсинг файла с должностями
        $data = simplexml_load_file(storage_path()."/DB/Positions.xml");
        foreach ($data->databas->table as $key => $value) {
          # code...
          Position::firstOrCreate(
              array(
                'name_position' => $value->column[1],
                'default_salary' => $value->column[2],
                'level' => $value->column[3]
              )
            );
        }
    }
}
