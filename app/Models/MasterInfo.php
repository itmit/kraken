<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterInfo extends Model
{
    use SoftDeletes;
    
    protected $table = 'master_infos';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
