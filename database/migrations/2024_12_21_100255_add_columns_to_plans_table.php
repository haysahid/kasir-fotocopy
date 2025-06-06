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
        Schema::table('plans', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->enum('duration_type', ['days', 'months', 'years'])->default('months')->after('price');
            $table->integer('duration')->default(0)->after('duration_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('duration_type');
            $table->dropColumn('duration');
        });
    }
};
