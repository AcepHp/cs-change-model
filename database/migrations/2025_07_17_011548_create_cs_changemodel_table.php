<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsChangemodelTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cs_changemodel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area', 9);
            $table->integer('line')->nullable();
            $table->string('model', 50)->nullable();
            $table->integer('list')->nullable();
            $table->string('station', 20)->nullable();
            $table->string('check_item', 200)->nullable();
            $table->string('standard', 100)->nullable();
            $table->string('actual', 50)->nullable();
            $table->string('trigger', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cs_changemodel');
    }
}
