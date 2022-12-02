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
        Schema::table('authorized_cards', function (Blueprint $table) {
            $table->string('authorization_code')->nullable()->change();
            $table->string('card_type')->nullable()->change();
            $table->string('last4')->nullable()->change();
            $table->string('exp_month')->nullable()->change();
            $table->string('exp_year')->nullable()->change();
            $table->string('bin')->nullable()->change();
            $table->string('bank')->nullable()->change();
            $table->string('signature')->nullable()->change();
            $table->string('reference')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('authorized_cards', function (Blueprint $table) {
            $table->string('authorization_code')->change();
            $table->string('card_type')->change();
            $table->string('last4')->change();
            $table->string('exp_month')->change();
            $table->string('exp_year')->change();
            $table->string('bin')->change();
            $table->string('bank')->change();
            $table->string('signature')->change();
            $table->string('reference')->change();
        });
    }
};
