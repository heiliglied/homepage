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
            $table->id();
			$table->string('user_id', 64)->index();
			$table->string('password', 192);
			$table->unsignedTinyInteger('roll');
			$table->string('name', 80)->nullable();
			$table->string('email', 80)->nullable();
			$table->string('contact', 24)->nullable();
			$table->string('email_verified_at', 192)->nullable();
			$table->date('password_change_date')->nullable();
			$table->rememberToken();
			$table->string('social_path', 32)->nullable();
			$table->string('social_key', 192)->nullable();
			$table->enum('except', ['Y', 'N', 'R'])->nullable()->default('N');
			$table->datetime('excepted_at')->nullable();
            $table->timestamps();
        });
		
		Schema::create('user_roll', function (Blueprint $table) {
            $table->unsignedTinyInteger('roll')->unique();
            $table->string('name', 64);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->unique();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->foreignId('user_id')->nullable();
			#$table->unsignedBigInteger('web_user_id')->nullable();
			#$table->unsignedBigInteger('admin_user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
