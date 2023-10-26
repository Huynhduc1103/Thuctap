<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates'; // Tên của bảng
    public $timestamps = false;
    protected $fillable = ['timer', 'type', 'data', 'event_id'];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id'); // Quan hệ many-to-one: Một log thuộc về một người dùng
    }
}
