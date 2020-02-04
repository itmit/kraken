<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'clients';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    protected $username = 'name';

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername()
    {
        return 'name';
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function getClientInfo()
    {
        return $this->hasOne(ClientInfo::class, 'client_id')->first();
    }

    public function getMasterInfo()
    {
        return $this->hasOne(MasterInfo::class, 'master_id')->first();
    }
}
