<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    public $timestamps = false;
    protected $table = 'logs'; // Tên của bảng

    protected $fillable = ['user_id', 'message_id', 'senddate', 'status_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // Quan hệ many-to-one: Một log thuộc về một người dùng
    }
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id', 'id'); // Quan hệ many-to-one: Một log thuộc về một người dùng
    }
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id'); // Quan hệ many-to-one: Một log thuộc về một người dùng
    }
}
