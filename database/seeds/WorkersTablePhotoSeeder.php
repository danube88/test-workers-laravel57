<?php

use Illuminate\Database\Seeder;
use App\Worker;

class WorkersTablePhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $workers = Worker::all();

        foreach ($workers as $worker) {
          // code...
          Worker::find($worker->id)->update([
            'photo' => $worker->id.'.jpg'
          ]);
        }
    }
}
