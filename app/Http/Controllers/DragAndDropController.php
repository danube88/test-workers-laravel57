<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Response;
use App\Subordination;

class DragAndDropController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function drop(Request $request)
    {
      // code...
      $input = $request->except('_token');

      $rules =
      [
          'id' => 'required|numeric',
          'idged' => 'required|numeric'
      ];
      $validator = Validator::make($input, $rules);
      if ($validator->fails()) {
        return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
      } else {
        //Subordination::where('subordinate_id',$input['idged'])->updateOrCreate();
        Subordination::updateOrCreate(
          ['subordinate_id'=>$input['idged']],
          ['head_id'=>$input['id'],
          'subordinate_id'=>$input['idged']]);
        return 'Перемещение выполено удачно';
      }
    }
}
