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
            $table->enum('identity_type', ['bvn', 'voters_card', 'nin', 'drivers_license', 'passport']);
            $table->string('identity_number')->unique();
            $table->string('id_base64_string')->nullable();
            $table->string('passport_base64_string')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('reference')->nullable();
            $table->json('payload')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('gender', ['m', 'male', 'f', 'female']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
