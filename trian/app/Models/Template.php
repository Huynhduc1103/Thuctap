<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates'; // Tên của bảng
    public $timestamps = false;
    protected $fillable = ['timer', 'type', 'data', 'message_id', 'event_id'];

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id', 'id'); // Quan hệ many-to-one: Một log thuộc về một người dùng
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id'); // Quan hệ many-to-one: Một log thuộc về một người dùng
    }
}
