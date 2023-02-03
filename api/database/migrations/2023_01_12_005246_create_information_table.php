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
        Schema::create('information', function (Blueprint $table) {
            $table->id();
            $table->json('welcomeSection');
            $table->json('servicesSection');
            $table->json('CharacteristicsSection');
            $table->json('footerSection');
            $table->string('facebookUrl');
            $table->string('instagramUrl');
            $table->string('whatsappNumber');
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
        Schema::dropIfExists('information');
    }
};
