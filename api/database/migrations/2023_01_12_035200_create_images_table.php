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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('imageTitle');
            $table->longText('imageDescription');
            $table->string('shortDescription');
            $table->unsignedBigInteger('categoryId');
            $table->integer('sliderStatus')->default(0);//0 apagado 1 encendido
            $table->integer('imageStatus')->default(1);
            $table->foreign('categoryId')->references('id')->on('categories');
            $table->string('pathImage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
};
