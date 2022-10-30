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
            $table->string('registered_business_name')->nullable()->nullable();
            $table->string('rc_number')->nullable()->nullable();
            $table->timestamp('date_of_registration')->nullable();
            $table->string('reg_certificate')->nullable()->nullable();
            $table->string('type_of_company')->nullable();
            $table->string('cac_document')->nullable();
            $table->timestamp('cac_uploaded_at')->nullable()->nullable();
            $table->string('tin_number')->nullable()->nullable();
            $table->timestamp('tin_verified_at')->nullable();
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
            $table->dropColumn([
                'registered_business_name',
                'rc_number',
                'date_of_registration',
                'type_of_company',
                'tin_number',
                'tin_verified_at'
            ]);
        });
    }
};
