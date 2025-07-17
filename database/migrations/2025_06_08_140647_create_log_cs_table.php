<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogCsTable extends Migration
{
    public function up()
    {
        Schema::create('log_cs', function (Blueprint $table) {
            $table->bigIncrements('id_log');
            $table->string('area', 50);
            $table->string('line', 50);
            $table->string('model', 50);
            $table->string('shift', 10)->nullable();
            $table->date('date')->nullable();
            $table->string('status', 20)->nullable();
            $table->string('image', 255)->nullable(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_cs');
    }
}
