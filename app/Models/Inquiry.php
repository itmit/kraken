<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use SoftDeletes;
    
    protected $table = 'inquiries';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    public function getInquiryDetail()
    {
        return $this->hasOne(InquiryDetail::class, 'inquiry_id')->first();
    }

    public function getClient()
    {
        return $this->hasOne(ClientInfo::class, 'client_id', 'client_id')->first();
    }

    public function getMaster()
    {
        return $this->hasOne(MasterInfo::class, 'master_id')->first();
    }
}
