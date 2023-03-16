<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('crawls', function (Blueprint $table) {
            $table->integer('avg_word_count')->default(0)->change();
            $table->integer('avg_title_length')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('crawls', function (Blueprint $table) {
            $table->decimal('avg_word_count')->default(0)->change();
            $table->decimal('avg_title_length')->default(0)->change();
        });
    }
};
