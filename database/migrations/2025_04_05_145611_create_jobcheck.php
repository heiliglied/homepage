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
        Schema::create('jobcheck', function (Blueprint $table) {
            $table->id();
			$table->string('jobkey')->comment('job에 사용할 고유 키값')->unique();
			$table->char('status', 1)->comment('진행상태 S: 시작, I: 진행중, E: 완료');
			$table->string('return')->comment('return value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobcheck');
    }
};
