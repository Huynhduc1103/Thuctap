<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    public $timestamps = false;
    protected $table = 'logs'; // Tên của bảng

    protected $fillable = ['user_id', 'template_id', 'senddate', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // Quan hệ many-to-one: Một log thuộc về một người dùng
    }
    public function message()
    {
        return $this->belongsTo(Template::class, 'template_id', 'id'); // Quan hệ many-to-one: Một log thuộc về một người dùng
    }
}
