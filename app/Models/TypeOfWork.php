<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeOfWork extends Model
{
    use SoftDeletes;
    
    protected $table = 'type_of_works';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
