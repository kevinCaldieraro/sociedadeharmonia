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
        Schema::create('member_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('patrimonial_member_id')->nullable();
            $table->enum('type', ['patrimonial', 'patrimonial_spouse', 'affiliated']);
            $table->date('join_date');
            $table->string('relationship')->nullable();
            $table->boolean('is_exempt')->default(false);
            $table->date('patrimonial_purchase_date')->nullable();
            $table->decimal('patrimonial_value', 10, 2)->unsigned()->nullable();
            $table->enum('status', ['ativo', 'desativado'])->default('ativo');
            $table->timestamps();
            
            $table->foreign('patrimonial_member_id')
                ->references('id')
                ->on('members')
                ->cascadeOnDelete();
            $table->unique(['member_id', 'type']);
        });

        DB::statement("
            ALTER TABLE member_role
            ADD CONSTRAINT chk_member_role_validations
            CHECK (
                (type = 'patrimonial' AND patrimonial_member_id IS NULL AND patrimonial_purchase_date IS NOT NULL AND patrimonial_value IS NOT NULL AND is_exempt = false)
                OR
                (type = 'patrimonial_spouse' AND patrimonial_member_id IS NOT NULL AND patrimonial_purchase_date IS NULL AND patrimonial_value IS NULL AND is_exempt = true)
                OR
                (type = 'affiliated' AND patrimonial_member_id IS NOT NULL AND patrimonial_purchase_date IS NULL AND patrimonial_value IS NULL)
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_role');
    }
};
