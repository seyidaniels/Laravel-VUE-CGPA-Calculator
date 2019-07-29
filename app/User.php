<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];


    public function gpas () {
        return $this->hasMany(GPA::class);
    }

    public function cpga() {
        if ($this->gpas()->count() == 0 ) return 0.00;
        $cgpa = $this->gpas()->sum('total_score') / $this->gpas()->sum('total_units');
        return number_format((float)$cgpa, 2, '.', ''); 
    }

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
}
