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
        Schema::table('merchant_details', function (Blueprint $table) {
            $table->string('address_confirmation')->nullable();
            $table->boolean('address_verified_at')->default(false);
            $table->boolean('upload_successful')->default(false);
            $table->string('disk')->default('public');
       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_details', function (Blueprint $table) {
            $table->dropColumn(['address_confirmation', 'address_verified_at', 'upload_successfull', 'disk']);

        });
    }
};
