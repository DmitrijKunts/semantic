<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cats', function (Blueprint $table) {
            $table->id('id');
            $table->integer('p_id');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('text')->nullable();

            // $table->primary('id');
            $table->index('p_id');
            $table->index('name');
            $table->index('slug');

            // $table->foreign('p_id')->references('id')->on('cats');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cats');
    }
};
