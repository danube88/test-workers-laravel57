<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HierarchyController extends Controller
{
    //
    public function index()
    {
      // code...
      if(view()->exists('hierarchy')){

      $workers = DB::table('workers as w')
                ->leftJoin('positions as p', 'p.id', '=', 'w.position_id')
                ->leftJoin('subordinations as s', 's.subordinate_id', '=', 'w.id')
                ->leftJoin('subordinations as sb', 'sb.head_id', '=', 'w.id')
                ->select([
                  "w.id",
                  DB::raw("CONCAT(w.surname,' ',w.name,' ',w.patronymic) as nameWorker"),
                  "w.reception_date",
                  "w.salary",
                  "p.id as id_p",
                  "p.level",
                  "p.name_position",
                  "s.head_id",
                  DB::raw("COUNT(sb.subordinate_id) as count")
                ])->groupBy(['w.id','s.head_id','w.surname','w.name','w.patronymic','w.reception_date','w.salary','p.id','p.level','p.name_position'])->get();
        return view('hierarchy',['workers' => $workers]);
      } else {
        abort(404);
      }
    }
}
