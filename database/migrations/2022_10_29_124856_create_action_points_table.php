<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_points', function (Blueprint $table) {
            $table->id();
            $table->morphs('rewardable');
            $table->string('performed_action');
            $table->integer('point');
            $table->integer('is_active')->default(true);
            $table->string('reference')->default(Str::random(10));

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
        Schema::dropIfExists('action_points');
    }
};
