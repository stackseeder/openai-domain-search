<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->integer('csv_id')->nullable();
            $table->string('name')->nullable();
            $table->string('org_name')->nullable();
            $table->string('domain')->unique();
            $table->string('country');
            $table->string('type');
            $table->string('category');
            $table->string('popularity_index');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sites');
    }
};
