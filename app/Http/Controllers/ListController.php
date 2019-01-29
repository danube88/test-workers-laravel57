<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use DataTables;

class ListController extends Controller
{
    //
    public function index()
    {
      // code...
      if(view()->exists('list')){
        return view('list');
      } else {
        abort(404);
      }
    }

    public function dataWorkers()
    {
      $workers = DB::table('workers as w')
            ->leftJoin('positions as p', 'p.id', '=', 'w.position_id')
            ->leftJoin('subordinations as s', 's.subordinate_id', '=', 'w.id')
            ->leftJoin('workers as wb', 'wb.id', '=', 's.head_id')
            ->select([
              "w.table_number",
              "w.photo",
              "w.birthday",
              DB::raw("CONCAT(w.surname,' ',w.name,' ',w.patronymic) as nameWorker"),
              "w.reception_date",
              "w.salary",
              "p.id as id_p",
              "p.level",
              "p.name_position as position",
              DB::raw("CONCAT(
                IFNULL(wb.surname,' '),
                ' ',
                IFNULL(wb.name,' '),
                ' ',
                IFNULL(wb.patronymic,' ')) as nameHead")
            ]);

      return Datatables::of($workers)
      ->filterColumn('nameHead', function ($query, $keyword) {
        $query->whereRaw("CONCAT(
          IFNULL(wb.surname,' '),
          ' ',
          IFNULL(wb.name,' '),
          ' ',
          IFNULL(wb.patronymic,' ')) like ?", ["%$keyword%"]);
      })
      ->filterColumn('table_number', function ($query, $keyword) {
        $query->whereRaw("w.table_number like ?", ["%$keyword%"]);
      })
      ->filterColumn('position', function ($query, $keyword) {
        $query->whereRaw("p.name_position like ?", ["%$keyword%"]);
      })
      ->filterColumn('nameWorker', function ($query, $keyword) {
        $query->whereRaw("CONCAT(w.surname,' ',w.name,' ',w.patronymic) like ?", ["%$keyword%"]);
      })
      ->editColumn('reception_date', function ($worker) {
        return $worker->reception_date ? with(new Carbon($worker->reception_date))->format('d.m.Y') : '';
      })
      ->filterColumn('reception_date', function ($query, $keyword) {
        $query->whereRaw("DATE_FORMAT(w.reception_date,'%m.%d.%Y') like ?", ["%$keyword%"]);
      })
      ->editColumn('birthday', function ($worker) {
        return $worker->birthday ? with(new Carbon($worker->birthday))->format('d.m.Y') : '';
      })
      ->filterColumn('birthday', function ($query, $keyword) {
        $query->whereRaw("DATE_FORMAT(w.birthday,'%m.%d.%Y') like ?", ["%$keyword%"]);
      })
      ->editColumn('salary', function ($worker) {
        return number_format($worker->salary, 2, ',', ' ').' руб.';
      })
      ->filterColumn('salary', function ($query, $keyword) {
        $query->whereRaw("w.salary like ?", ["%$keyword%"]);
      })
      ->editColumn('photo', function ($worker) {
        if(($worker->photo != null)&&(file_exists(public_path()."img/photo/mini/".$worker->photo))){
          return $worker->photo;
        } else {
          return '../img/example_mini.jpg';
        };
      })
      ->make(true);
    }
}
