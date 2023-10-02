<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
        protected $table = 'users'; // Tên của bảng
    
        protected $fillable = ['email', 'password', 'phone', 'birthday'];
    
        public function logs()
        {
            return $this->hasMany(Log::class, 'user_id', 'id'); // Quan hệ one-to-many: Một người dùng có nhiều logs
        }
}
