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
        Schema::create('goods', function (Blueprint $table) {
            $table->softDeletes();

            $table->id();
            $table->string('sku');
            $table->string('code')->unique();
            $table->string('link');
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', $precision = 8, $scale = 2);
            $table->decimal('oldprice', $precision = 8, $scale = 2);
            $table->string('currency');
            $table->text('pictures')->nullable();
            $table->text('alts')->nullable();
            $table->string('vendor')->nullable();
            $table->string('model')->nullable();
            $table->text('desc')->nullable();
            $table->text('summary')->nullable();
            $table->text('tech')->nullable();
            $table->text('equip')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('name');
            $table->index('slug');
            $table->index('price');
            $table->index('vendor');
            $table->index('model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');
    }
};
