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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('disabled_at')->nullable()->after('email');
            $table->foreignId('role_id')->constrained('roles')->onUpdate('cascade')->onDelete('no action')->after('email');
            $table->string('avatar')->nullable()->after('email');
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
