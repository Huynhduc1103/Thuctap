<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages'; // Tên của bảng
    
        protected $fillable = ['eventname', 'desribe', 'eventdate', 'template_id'];
    
        public function logs()
        {
            return $this->hasMany(Logs::class, 'message_id', 'id'); // Quan hệ one-to-many: Một message có nhiều logs
        }
        public function template()
        {
            return $this->belongsTo(Template::class, 'template_id', 'id'); // Quan hệ many-to-one: Một log thuộc về một người dùng
        }
}
