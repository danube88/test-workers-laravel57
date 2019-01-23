<?php

use Illuminate\Database\Seeder;
use App\Worker;

class WorkersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //Дата начала и конца для рождения
        $startB = strtotime('1 Jan 1975');
        $endB = strtotime('1 Jan 1995');

        //парсинг xml файла с работниками
        $data = simplexml_load_file(storage_path()."/DB/Workers.xml");

        foreach ($data->databas->table as $key => $value) {

          //генерация Дня рождения
          $dataB = mt_rand($startB,$endB);
          //генерация Дня приема
          $dataW = new DateTime(date('Y-m-d',$dataB));
          //Отклонения от даты рождения (18-22 года 1-12 месяцев 1-31 день)
          $dataW->modify(''.rand(18,22).' year')->modify(''.rand(1,12).' month')->modify(''.rand(1,31).' day');

          $fio = explode(" ", $value->column[1]);

          Worker::firstOrCreate(
            array(
              'table_number' => $value->column[0]+100000,
              'surname' => $fio[0],
              'name' => $fio[1],
              'patronymic' => $fio[2],
              'birthday' => date('Y-m-d',$dataB),
              'position_id' => $value->column[2],
              'salary' => $value->column[3],
              'reception_date' => $dataW->format('Y-m-d')
            )
          );
        }
    }
}
