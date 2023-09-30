<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('eventname');
            $table->string('desribe');
            $table->date('eventdate');
            // Định nghĩa cột chứa khóa ngoại
            $table->unsignedBigInteger('template_id'); // Định nghĩa kiểu dữ liệu và tên cột
            $table->foreign('template_id')
            ->references('id')
            ->on('templates')
            ->onDelete('cascade'); // onDelete('cascade') sẽ xóa tất cả bản ghi con khi bản ghi cha bị xóa
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_messages');
    }
}
