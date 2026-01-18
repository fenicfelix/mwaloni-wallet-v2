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
            // rename name to first_name and give it a length of 30
            $table->renameColumn('name', 'first_name');
            $table->string('first_name', 30)->change();

            $table->string('last_name', 30)->after('first_name');
            $table->string('phone_number', 20)->after('last_name');
            $table->string('username', 50)->nullable()->after('email_verified_at');
            $table->string('api_key')->nullable()->after('username');
            $table->boolean("active")->default(true)->after('password');
            $table->boolean("force_password_reset")->default(false)->after('active');
            $table->foreignId('added_by')->nullable()->references("id")->on("users")->onDelete("set null")->after('force_password_reset');
            $table->foreignId('updated_by')->nullable()->references("id")->on("users")->onDelete("set null")->after('added_by');
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
