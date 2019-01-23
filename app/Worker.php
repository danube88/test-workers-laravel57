<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    //
    protected $fillable = [
        'table_number',
        'surname',
        'name',
        'patronymic',
        'birthday',
        'position_id',
        'salary',
        'reception_date'
    ];
}
