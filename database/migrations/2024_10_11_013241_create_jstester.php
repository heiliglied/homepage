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
        Schema::create('jstester', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('user_id')->nullable()->comment('사용자 ID');
			$table->string('session_id')->comment('세션 값');
			$table->string('page_key')->comment('부여 url');
			$table->string('view_name')->comment('사용자 표시 이름');
			$table->json('code')->comment('페이지 소스값');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jstester');
    }
};
