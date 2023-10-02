<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status'; // Tên của bảng
    
        protected $fillable = ['statusmessage'];
    
        public function logs()
        {
            return $this->hasMany(Log::class, 'status_id', 'id'); // Quan hệ one-to-many: Một người dùng có nhiều logs
        }
}
