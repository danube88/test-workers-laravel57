<?php

use Illuminate\Database\Seeder;
use App\Subordination;

class SubordinationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //парсинг файла с начальник - подчиненный
        $data = simplexml_load_file(storage_path()."/DB/Subordinations.xml");
        foreach ($data->databas->table as $key => $value) {
          # code...
          Subordination::firstOrCreate(
              array(
                'head_id' => $value->column[1],
                'subordinate_id' => $value->column[2]
              )
            );
        }
    }
}
