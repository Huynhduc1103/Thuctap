<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $timestamps = false;
    protected $table = 'messages'; // Tên của bảng

    protected $fillable = ['messagetype', 'type', 'event_id'];

    public function logs()
    {
        return $this->hasMany(Logs::class, 'message_id', 'id'); // Quan hệ one-to-many: Một message có nhiều logs
    }

    public function template()
    {
        return $this->hasMany(Template::class, 'message_id', 'id'); // Quan hệ one-to-many: Một message có nhiều logs
    }
}
