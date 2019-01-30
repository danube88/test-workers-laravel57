<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Position;
use App\Worker;
use App\Subordination;
use Response;
use Validator;
use Image;

class CRUDController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input = $request->except('_token');

        $rules = [
            'head_id' => 'numeric',
            'table_number' => 'required|min:6|max:6|unique:workers',
            'photo' => 'file|image|max:1024|mimes:jpeg,jpg,bmp,png',
            'photodel' => 'boolean',
            'surname' => 'required|min:2|max:128',
            'name' => 'required|min:2|max:128',
            'patronymic' => 'required|min:2|max:128',
            'birthday' => 'required|date|before_or_equal:'.date("Y-m-d").'',
            'position' => 'required|numeric',
            'salary' => 'required|numeric',
            'reception_date' => 'required|date|before_or_equal:'.date("Y-m-d").''
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
          return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } else {
          //$position = Position::where('id', '=', $input['position'])->first()->id;
          $worker = Worker::create([
            'surname' => $input['surname'],
            'name' => $input['name'],
            'patronymic' => $input['patronymic'],
            'table_number' => $input['table_number'],
            'birthday' => $input['birthday'],
            'position_id' => $input['position'],
            'salary' => $input['salary'],
            'reception_date' => $input['reception_date']
          ]);

          if($request->hasFile('photo') && $input['photodel'] == false){
            $file = $request->file('photo');
            $input['photo'] = $worker->id.'.'.$file->getClientOriginalExtension();
            //$file->move(public_path().'/img/photo',$input['photo']);
            Image::make($file)->resize(200, 300)->save(public_path().'/img/photo/'.$input['photo'],100);

            $img = Image::make(public_path().'/img/photo/'.$input['photo'])->resize(70, 105);
            $img->save(public_path().'/img/photo/mini/'.$input['photo'],100);

            Worker::find($worker->id)->update([
              'photo' => $input['photo']
            ]);
          } else {
            Worker::find($worker->id)->update([
              'photo' => NULL
            ]);
          }

          if ($input['head_id'] != 0) {
            Subordination::create([
              'head_id' => $input['head_id'],
              'subordinate_id' => $worker->id
            ]);
          }
          return Response::json(['data'=>'Карточка сотрудника №'.$input['table_number'].' создана в БД']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $worker = Worker::where('table_number','=',$id)->first();
        $input = $request->except('_token');

        $rules = [
            'head_id' => 'numeric',
            'table_number' => ['required','min:6','max:6',Rule::unique('workers')->ignore($worker->id),],
            'photo' => 'file|image|max:2024|mimes:jpeg,jpg,bmp,png',
            'photodel' => 'boolean',
            'surname' => 'required|min:2|max:128',
            'name' => 'required|min:2|max:128',
            'patronymic' => 'required|min:2|max:128',
            'birthday' => 'required|date|before_or_equal:'.date("Y-m-d").'',
            'position' => 'required',
            'salary' => 'required|numeric',
            'reception_date' => 'required|date|before_or_equal:'.date("Y-m-d").''
        ];
        $validator = Validator::make($input,$rules);
        if ($validator->fails()) {
          return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } else {
          $position = Position::where('id', '=', $input['position'])->first();
          Worker::find($worker->id)->update([
            'surname' => $input['surname'],
            'name' => $input['name'],
            'patronymic' => $input['patronymic'],
            'table_number' => $input['table_number'],
            'birthday' => $input['birthday'],
            'position_id' => $position['id'],
            'salary' => $input['salary'],
            'reception_date' => $input['reception_date'],
          ]);

          if($request->hasFile('photo')){
            $file = $request->file('photo');
            $input['photo'] = $worker->id.'.'.$file->getClientOriginalExtension();
            //$file->move(public_path().'/img/photo',$input['photo']);

            Image::make($file)->resize(200, 300)->save(public_path().'/img/photo/'.$input['photo'],100);

            $img = Image::make(public_path().'/img/photo/'.$input['photo'])->resize(70, 105);
            $img->save(public_path().'/img/photo/mini/'.$input['photo'],100);

            Worker::find($worker->id)->update([
              'photo' => $input['photo']
            ]);
          }

          if ($input['head_id'] != 0) {
            $subordination = Subordination::updateOrCreate(
              ['subordinate_id' => $worker->id],
              ['head_id' => $input['head_id']]
            );
          } else {
            Subordination::where('subordinate_id','=',$worker->id)->delete();
          }

          if ($input['photodel'] && $worker->photo != null) {
            if(file_exists(public_path().'/img/photo/mini/'.$worker->photo)){
              unlink(public_path().'/img/photo/mini/'.$worker->photo);
            }
            if(file_exists(public_path().'/img/photo/'.$worker->photo)){
              unlink(public_path().'/img/photo/'.$worker->photo);
            }
            Worker::find($worker->id)->update([
              'photo' => NULL
            ]);
          }

          $photo = Worker::where('id',$worker->id)->first()->photo;
          return Response::json(array(
            'data'=>'Карточка сотрудника №'.$input['table_number'].' изменена',
            //'img'=>(file_exists(public_path().'/img/photo/mini/'.$photo))?'../img/photo/mini/'.$photo.'?'.rand():'../img/example_mini.jpg'
          ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $worker = Worker::where('table_number','=',$id)->first();
        $head = Subordination::where('head_id','=',$worker->id)->count();
        if($head > 0){
          return Response::json(array('errors' => ['data'=>'Данный сотрудник имеет подчиненных, и не может быть удален']));
        } else {
          Subordination::where('subordinate_id','=',$worker->id)->delete();
          if($worker->photo != NULL){
            if(file_exists(public_path().'/img/photo/mini/'.$worker->photo)){
              unlink(public_path().'/img/photo/mini/'.$worker->photo);
            }
            if(file_exists(public_path().'/img/photo/'.$worker->photo)){
              unlink(public_path().'/img/photo/'.$worker->photo);
            }
          }
          Worker::find($worker->id)->delete();
          return Response::json(['data'=>'Карточка '.$worker->table_number.' сотрудника удалена']);
        }
    }
}
