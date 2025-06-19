<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('plan_id')->nullable()->default(null)
                ->comment('Foreign key to plans table');
            // user_id: unsigned FK to users
            $table->unsignedBigInteger('user_id')->nullable()->default(null)
                ->comment('Foreign key to users table');

            // Amount charged (in major currency units, e.g. dollars)
            $table->decimal('amount', 10, 2)
                ->comment('Amount charged');

            // other potential columns
            // auth code
            // last4 of card

            // Status of the charge: success or failed
            $table->unsignedTinyInteger('status')
                ->default(0)
                ->comment('100=success, 500=general decline, 501=some specific failure reason, 502=insufficient funds');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
