<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientInfo extends Model
{
    protected $table = 'client_infos';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
