<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Validator;
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
                ->where('s.head_id','=',null)
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
        foreach ($workers as $value) {
          $array[] = $value->id;
        }
        $workers_two = DB::table('workers as w')
                  ->leftJoin('positions as p', 'p.id', '=', 'w.position_id')
                  ->leftJoin('subordinations as s', 's.subordinate_id', '=', 'w.id')
                  ->leftJoin('subordinations as sb', 'sb.head_id', '=', 'w.id')
                  ->whereIn('s.head_id',$array)
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
          $workers = array_merge($workers->toArray(), $workers_two->toArray());
          return view('hierarchy',['workers' => $workers]);
      } else {
        abort(404);
      }
    }

    public function dataHierarchy(Request $request)
    {
      $input = $request->except('_token');

      $rules = [
              'id' => 'required|numeric'
              ];
      $validator = Validator::make($input, $rules);
      if ($validator->fails()) {
        return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
      } else {
        $workers = DB::table('workers')
              ->leftJoin('positions', 'positions.id', '=', 'workers.position_id')
              ->leftJoin('subordinations', 'subordinations.subordinate_id', '=', 'workers.id')
              ->where('subordinations.head_id','=',$input['id'])
              ->get(array('workers.id',
                          DB::raw("CONCAT(workers.surname,' ',workers.name,' ',workers.patronymic) as nameWorker"),
                          'positions.name_position',
                          'subordinations.head_id'
                        ));

        $node = array();
        foreach ($workers as $worker) {
          $subordination = DB::table('subordinations')->where('subordinations.head_id','=',$worker->id)->count();
          $isFolder = ($subordination != 0)?1:0;
          $node[] = '{ "id": "'.$worker->id.'","title": "<h6> '.$worker->name_position.'</h6>'.$worker->nameWorker.'","isFolder": "'.$isFolder.'"}';
        }
        return Response::json($node);
      }
    }
}
