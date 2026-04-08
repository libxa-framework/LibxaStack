<?php

declare(strict_types=1);

use Libxa\Atlas\Migrations\Migration;
use Libxa\Atlas\Schema\Blueprint;
use Libxa\Atlas\Schema\Schema;

/**
 * Migration: CreateUsersTable
 * 
 * Generated on 2026-04-07
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
