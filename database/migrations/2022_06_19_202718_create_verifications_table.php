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
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('identity_type', ['bvn', 'voters_card', 'nin', 'drivers_license']);
            $table->string('identity_number')->unique();
            $table->string('id_base64_string');
            $table->string('passport_base64_string');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name');
            $table->string('date_of_birth');
            $table->string('reference');
            $table->string('payload');
            $table->string('phone_number')->unique();
            $table->enum('gender', ['male', 'female']);
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
        Schema::dropIfExists('verifications');
    }
};
