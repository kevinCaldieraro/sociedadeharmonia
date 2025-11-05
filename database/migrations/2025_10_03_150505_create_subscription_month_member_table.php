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
        Schema::create('subscription_month_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_member_id')
                ->constrained('subscription_member')
                ->cascadeOnDelete();
            $table->enum('status', ['paga', 'pendente', 'vencida', 'isenta'])->default('pendente');
            $table->date('paid_at')->nullable();
            $table->date('expiration_date');
            $table->decimal('value', 10, 2)->unsigned()->nullable();
            $table->tinyInteger('month');
            $table->integer('year');
            $table->string('payment_method', 255)->nullable();
            $table->text('payment_proof_link')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE subscription_month_member ADD CONSTRAINT check_month CHECK (month BETWEEN 1 AND 12)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_month_member');
    }
};
