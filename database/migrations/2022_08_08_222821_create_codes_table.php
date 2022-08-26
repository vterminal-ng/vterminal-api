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
            $table->enum('transaction_type', ['withdrawal', 'deposit']);
            $table->enum('status', ['pending', 'active', 'complete', 'cancelled', 'expired'])->default('pending');
            $table->integer('subtotal_amount');
            $table->integer('total_amount');
            $table->integer('charge_amount');
            $table->string('reference');
            $table->enum('charge_from', ['cash', 'card']);
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
