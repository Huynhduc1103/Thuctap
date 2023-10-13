<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $timestamps = false;
    protected $table = 'messages'; // Tên của bảng

    protected $fillable = ['eventname', 'desribe', 'eventdate', 'type'];

    public function logs()
    {
        return $this->hasMany(Logs::class, 'message_id', 'id'); // Quan hệ one-to-many: Một message có nhiều logs
    }
}
