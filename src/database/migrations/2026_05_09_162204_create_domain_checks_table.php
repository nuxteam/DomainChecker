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
        Schema::create('domain_checks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('domain_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('is_up')->default(false);
            $table->integer('status_code')->nullable();
            $table->integer('response_time')->nullable();
            $table->text('error')->nullable();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_checks');
    }
};
