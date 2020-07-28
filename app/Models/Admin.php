<?php

namespace App\Models;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Admin extends model implements AuthenticatableContract, AuthorizableContract
{
    use Notifiable, Authenticatable, Authorizable;

    protected $casts = [
        'is_binded' => 'boolean',
        'is_tfa'    => 'boolean',
        'locked'    => 'boolean'
    ];

    protected $columns = [
        'id',
        'name',
        'email',
        'tfa_secret',
        'is_binded',
        'is_tfa',
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'tfa_secret'
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function oneDrives()
    {
        return $this->hasMany(OneDrive::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function setting()
    {
        return $this->hasOne(Setting::class, 'admin_id', 'id');
    }
}
