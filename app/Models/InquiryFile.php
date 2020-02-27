<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InquiryFile extends Model
{
    use SoftDeletes;
    
    protected $table = 'inquiry_files';

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
