<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Silber\Bouncer\Database\Role;

class User extends Authenticatable
{
    use Notifiable, HasRolesAndAbilities;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user', 'id', 'company_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referral_code', 'user_unique_id');
    }

    public function referees()
    {
        return $this->hasMany(User::class, 'referral_code', 'user_unique_id');
    }
}
