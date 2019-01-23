<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subordination extends Model
{
    //
    protected $fillable = [
        'head_id',
        'subordinate_id',
    ];
}
