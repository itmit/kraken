<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientInfo extends Model
{
    protected $table = 'client_infos';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
