<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InquiryDetail extends Model
{
    use SoftDeletes;
    
    protected $table = 'inquiry_details';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    protected $dates = ['started_at'];

    public function getWork()
    {
        return $this->belongsTo(TypeOfWork::class, 'work')->first();
    }
}
