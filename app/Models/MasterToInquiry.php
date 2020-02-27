<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterToInquiry extends Model
{
    use SoftDeletes;
    
    protected $table = 'master_to_inquiries';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
