<?php

use Libxa\Atlas\Schema\Blueprint;
use Libxa\Atlas\DB;

class CreateJobsTable
{
    public function up(): void
    {
        DB::schema()->create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->text('payload');
            $table->integer('attempts')->default(0);
            $table->integer('reserved_at')->nullable();
            $table->integer('available_at');
            $table->integer('created_at');
        });
    }

    public function down(): void
    {
        DB::schema()->dropIfExists('jobs');
    }
}