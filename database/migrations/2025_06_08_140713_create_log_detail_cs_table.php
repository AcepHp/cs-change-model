<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogDetailCsTable extends Migration
{
    public function up()
    {
        Schema::create('log_detail_cs', function (Blueprint $table) {
            $table->bigIncrements('id_det');
            $table->unsignedBigInteger('id_log');
            $table->integer('list')->nullable();
            $table->string('station', 50);
            $table->string('check_item', 100);
            $table->string('standard', 100);
            $table->string('scanResult', 20)->nullable();
            $table->string('prod_status', 10)->nullable(); 
            $table->string('prod_checked_by', 50)->nullable();
            $table->timestamp('prod_checked_at')->nullable();
            $table->string('quality_status', 10)->nullable(); 
            $table->string('quality_checked_by', 50)->nullable();
            $table->timestamp('quality_checked_at')->nullable();

            $table->timestamps();

            $table->foreign('id_log')
                ->references('id_log')
                ->on('log_cs')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_detail_cs');
    }
}