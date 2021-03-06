<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use DataTables;
use App\Position;
use App\Worker;
use App\Subordination;
use Validator;
use Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(view()->exists('listAuth')){
          $positions = Position::all();
          $guide = DB::table('workers')
                    ->leftJoin('positions', 'positions.id', '=', 'workers.position_id')
                    ->where('positions.level','<=','4')
                    ->select(
                      'workers.id',
                      'workers.table_number',
                      DB::raw("CONCAT(workers.surname,' ',workers.name,' ',workers.patronymic) as nameWorker"),
                      'positions.id as id_p',
                      'positions.name_position')
                    ->orderBy('positions.id','asc')
                    ->get();
          return view('listAuth',['positions'=>$positions,'guide'=>$guide]);
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
        if($worker->photo != null){
          if(file_exists(public_path()."/img/photo/mini/".$worker->photo)){
            return '../img/photo/mini/'.$worker->photo.'?'.rand();
          } else {
            return '../img/example_mini.jpg';
          }
        } else {
          return '../img/example_mini.jpg';
        };
      })
      ->make(true);
    }

    public function listHead(Request $request)
    {
      $input = $request->except('_token');

      $messages = [
                  'required' =>'обязательное к передаче.'
                  ];
      $rules =  [
                'position' => 'required|numeric'
                ];
      $validator = Validator::make($input, $rules, $messages);
      if ($validator->fails()) {
        return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
      } else {

        $listHead = $this->listHeadStr(Position::where('id','=',$input['position'])->first()->level);

        return Response::json($listHead);
      }
    }

    public function dataWorker(Request $request)
    {
      $input = $request->except('_token');

      $messages = [
                  'required' =>'обязательное к передаче.'
                  ];
      $rules =  [
                'table_number' => 'required|numeric'
                ];
      $validator = Validator::make($input, $rules, $messages);
      if ($validator->fails()) {
        return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
      } else {

        $worker = Worker::where('table_number','=',$input['table_number'])
                  ->get();
        if($worker->isEmpty()){
          $error = "ID не верное сотрудника";
          return Response::json(array( 'errors' => $error ));
        }

        $worker = Worker::with('position')
                  ->with('subordination')
                  ->select('workers.*')
                  ->where('workers.table_number','=',$input['table_number'])
                  ->first();

        if ($worker->subordination != NULL){
          $headworker = Worker::with('position')
                        ->select([
                          'workers.*',
                          DB::raw("CONCAT(workers.surname,' ',workers.name,' ',workers.patronymic) as nameWorker")
                          ])
                        ->where('workers.id','=',$worker->subordination->head_id)
                        ->first();
          $worker['headworker'] = $headworker->table_number.". ".$headworker->nameWorker." / ".$headworker->position->name_position;
        } else {
          $worker['headworker'] = NULL;
        }
        unset($worker->subordination);
        $worker->positionName = $worker->position->id.". ".$worker->position->name_position;
        $worker->level = $worker->position->level;
        $listHead = $this->listHeadStr($worker->position->level);
        unset($worker->position);
        if (($worker->photo != NULL)&&(file_exists(public_path().'/img/photo/'.$worker->photo))) {
          $worker->photo = '../img/photo/'.$worker->photo.'?'.rand();
        } else {
          $worker->photo = '../img/example.jpg';
        }
        $worker->listHead = $listHead;
        return Response::json($worker);
      }
    }

    public function listHeadRedistribution(Request $request)
    {
      $input = $request->except('_token');

      $rules =
      [
          'id' => 'required|numeric',
          'position' => 'required|numeric'
      ];
      $validator = Validator::make($input, $rules);
      if ($validator->fails()) {
        return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
      } else {
        $guide = DB::table('workers')
                  ->leftJoin('positions', 'positions.id', '=', 'workers.position_id')
                  ->where('positions.id','=',$input['position'])
                  ->where('workers.id','!=',$input['id'])
                  ->select(
                    'workers.id',
                    'workers.table_number',
                    DB::raw("CONCAT(workers.surname,' ',workers.name,' ',workers.patronymic) as nameWorker"),
                    'positions.id as id_p',
                    'positions.name_position')
                  ->get();
        $array = '';
        foreach ($guide as $value) {
          $array = $array."<option data-id=\"$value->id\" data-idpos=\"$value->id_p\" value=\"$value->table_number: $value->nameWorker/$value->name_position\"></option>";
        }
        return Response::json($array);
      }
    }

    public function redistribution(Request $request)
    {
      $input = $request->except('_token');

      $rules =
      [
          'oneId' => 'required|numeric',
          'twoId' => 'required|numeric'
      ];
      $validator = Validator::make($input, $rules);
      if ($validator->fails()) {
        return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
      } else {
        Subordination::where('head_id',$input['oneId'])->update(['head_id'=>$input['twoId']]);
        return Response::json(array('data' =>'Перераспределение прошло успешно!'));
      }
    }

    protected function listHeadStr($level)
    {
      $workers = DB::table('workers')
            ->leftJoin('positions', 'positions.id', '=', 'workers.position_id')
            ->where('positions.level','<',$level)
            ->select([
              'workers.id',
              'workers.table_number',
              DB::raw("CONCAT(workers.surname,' ',workers.name,' ',workers.patronymic) as nameWorker"),
              'positions.name_position'
            ])
            ->orderBy('workers.table_number', 'asc')
            ->get();
      $str = "";
      foreach ($workers as $worker) {
        $str .= "<option data-id=\"$worker->id\" value=\"$worker->table_number. $worker->nameWorker / $worker->name_position\"></option>";
      }
      return $str;
    }
}
