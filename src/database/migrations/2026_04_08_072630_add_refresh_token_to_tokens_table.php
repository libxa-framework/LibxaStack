<?php

use Libxa\Atlas\Schema\Blueprint;
use Libxa\Atlas\DB;

class AddRefreshTokenToPersonalAccessTokensTable
{
    public function up(): void
    {
        DB::schema()->table('personal_access_tokens', function (Blueprint $table) {
            $table->string('refresh_token', 64)->nullable()->unique();
        });
    }

    public function down(): void
    {
        // Libxa Blueprint currently focuses on additions and CREATE TABLE.
        // For security reasons, manual schema cleanup for tokens is recommended.
    }
}
