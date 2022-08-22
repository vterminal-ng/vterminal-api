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
        Schema::create('codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('merchant_id')->nullable();
            $table->string('code')->unique();
            $table->string('transaction_type');
            $table->enum('status', ['pending', 'active', 'complete', 'cancelled'])->default('pending');
            $table->integer('amount');
            $table->integer('charge_amount');
            $table->boolean('charge_on_card');
            $table->string('vterminal_charge')->nullable();
            $table->string('merchant_charge')->nullable();

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
        Schema::dropIfExists('codes');
    }
};
