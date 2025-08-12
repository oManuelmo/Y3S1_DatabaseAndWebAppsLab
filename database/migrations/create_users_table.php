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
        Schema::create('users', function (Blueprint $table) {
            $table->id('userId');
            $table->string('firstName', 50);
            $table->string('lastName', 50);
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->boolean('isAdmin');
            $table->decimal('rate', 10, 2)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postalCode', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->decimal('bidBalance', 10, 2)->nullable();
            $table->unsignedBigInteger('imageId')->nullable();
            $table->date('birthDate');
            $table->rememberToken();
            $table->timestamps();
            
            $table->check('birthDate <= (CURRENT_DATE - INTERVAL \'18 YEARS\')');

            // Foreign key constraint with images table
            $table->foreign('imageId')->references('imageId')->on('images')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
