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
        $workers = $this->listWorkersForHierarchy(null);
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
        $workers = $this->listWorkersForHierarchy($input['id']);
        $node = array();
        foreach ($workers as $worker) {
          $isFolder = ($worker->count != 0)?1:0;
          $node[] = '{ "id": "'.$worker->id.'","head":"'.$worker->head_id.'","title": "<h6> '.$worker->name_position.'</h6>'.$worker->nameWorker.'","isFolder": "'.$isFolder.'"}';
        }
        return Response::json($node);
      }
    }

    protected function listWorkersForHierarchy($id){
      $workers = DB::table('workers as w')
              ->leftJoin('positions as p', 'p.id', '=', 'w.position_id')
              ->leftJoin('subordinations as s', 's.subordinate_id', '=', 'w.id')
              ->leftJoin('subordinations as sb', 'sb.head_id', '=', 'w.id')
              ->where('s.head_id','=',$id)
              ->select([
                "w.id",
                DB::raw("CONCAT(w.surname,' ',w.name,' ',w.patronymic) as nameWorker"),
                //"w.reception_date",
                //"w.salary",
                //"p.id as id_p",
                //"p.level",
                "p.name_position",
                "s.head_id",
                DB::raw("COUNT(sb.subordinate_id) as count")])
              ->groupBy(['w.id',
                        's.head_id',
                        'w.surname',
                        'w.name',
                        'w.patronymic',
                        /*'w.reception_date',
                        'w.salary',
                        'p.id',
                        'p.level',*/
                        'p.name_position'])
              ->get();
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
                  //"w.reception_date",
                  //"w.salary",
                  //"p.id as id_p",
                  //"p.level",
                  "p.name_position",
                  "s.head_id",
                  DB::raw("COUNT(sb.subordinate_id) as count")])
                ->groupBy(['w.id',
                          's.head_id',
                          'w.surname',
                          'w.name',
                          'w.patronymic',
                          /*'w.reception_date',
                          'w.salary',
                          'p.id',
                          'p.level',*/
                          'p.name_position'])
                ->get();
      $workers = array_merge($workers->toArray(), $workers_two->toArray());
      return $workers;
    }
}
