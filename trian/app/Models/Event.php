<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    
    public $timestamps = false;
    protected $table = 'events'; // Tên của bảng

    protected $fillable = ['eventname', 'eventdate'];

    public function template()
    {
        return $this->hasMany(Template::class, 'event_id', 'id'); // Quan hệ one-to-many: Một message có nhiều logs
    }
    public function logs()
    {
        return $this->hasMany(Logs::class, 'event_id', 'id'); // Quan hệ one-to-many: Một message có nhiều logs
    }
}
