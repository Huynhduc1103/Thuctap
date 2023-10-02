<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates'; // Tên của bảng
    public $timestamps = false;
        protected $fillable = ['notification', 'content'];
    
        public function message()
        {
            return $this->hasMany(Message::class, 'template_id', 'id'); // Quan hệ one-to-many: Một người dùng có nhiều logs
        }
}
