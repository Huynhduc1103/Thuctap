<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates'; // Tên của bảng
    public $timestamps = false;
        protected $fillable = ['notification', 'timer', 'type', 'data', 'message_id'];
    
        public function message()
        {
            return $this->hasMany(Message::class, 'message_id', 'id'); // Quan hệ one-to-many: Một người dùng có nhiều logs
        }
}
