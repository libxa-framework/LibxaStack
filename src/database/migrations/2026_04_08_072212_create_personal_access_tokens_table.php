<?php

use Libxa\Atlas\Schema\Blueprint;
use Libxa\Atlas\DB;

class CreatePersonalAccessTokensTable
{
    public function up(): void
    {
        DB::schema()->create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('tokenable_type');
            $table->integer('tokenable_id');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        DB::schema()->dropIfExists('personal_access_tokens');
    }
}