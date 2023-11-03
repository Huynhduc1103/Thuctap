<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Failed extends Model
{
    public $timestamps = false;
    protected $table = 'faileds'; // Tên của bảng

    protected $fillable = ['user_id', 'date', 'event_id', 'type', 'error'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // Quan hệ many-to-one: Một error thuộc về một người dùng
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id'); // Quan hệ many-to-one: Một log thuộc về một người dùng
    }
}
