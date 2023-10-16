<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Định nghĩa kiểu dữ liệu và tên cột
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade'); // onDelete('cascade') sẽ xóa tất cả bản ghi con khi bản ghi cha bị xóa
            $table->date('senddate');
            $table->string('status');
            $table->unsignedBigInteger('message_id'); // Định nghĩa kiểu dữ liệu và tên cột
            $table->foreign('message_id')
            ->references('id')
            ->on('messages')
            ->onDelete('cascade'); // onDelete('cascade') sẽ xóa tất cả bản ghi con khi bản ghi cha bị xóa
            $table->unsignedBigInteger('event_id')->nullable(); // Định nghĩa kiểu dữ liệu và tên cột
            $table->foreign('event_id')
            ->references('id')
            ->on('events')
            ->onDelete('cascade'); // onDelete('cascade') sẽ xóa tất cả bản ghi con khi bản ghi cha bị xóa
            $table->unique(['user_id', 'message_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}