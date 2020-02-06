<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    
    protected $table = 'departments';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    public function getDepartmentEmail()
    {
        return $this->hasOne(User::class, 'department_id')->first('email');
    }
}
