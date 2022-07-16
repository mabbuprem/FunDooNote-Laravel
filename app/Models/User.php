<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public static function updatePassword($user, $new_password)
    {
        $user->password = bcrypt($new_password);
        $user->save();
        return $user;
    }

    public static function getUserByEmail($email){
        $user = User::where('email', $email)->first();
        return $user;
    }

     /**
     * Mutator for first name : first letter of first name will changed to upper case 
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['firstname'] = ucfirst($value);
    }

    /**
     * Mutator for last name : first letter of last name will changed to upper case
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes['lastname'] = ucfirst($value);
    }

    /**
     * Accessor for first name attribute
     * When user is retrived from database, 
     * first letter of first name will be upper case and 
     * Mr/s. will be added while displaying
     */
    public function getFirstNameAttribute($value)
    {
        return 'Mr/s. ' . ucfirst($value);
    }

}

