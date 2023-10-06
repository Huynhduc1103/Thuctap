<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('notification');
            $table->datetime('timer');
            $table->string('type');
            $table->string('data');
            $table->unsignedBigInteger('message_id'); // Định nghĩa kiểu dữ liệu và tên cột
            $table->foreign('message_id')
            ->references('id')
            ->on('messages')
            ->onDelete('cascade'); // onDelete('cascade') sẽ xóa tất cả bản ghi con khi bản ghi cha bị xóa
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_templates');
    }
}
