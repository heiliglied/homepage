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
        Schema::create('board_reply', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('board_id')->index();
			$table->string('writer', 12);
			$table->unsignedBigInteger('user_id');         
            $table->longText('reply');
            $table->unsignedBigInteger('parent_id');
			$table->integer('depth');
			$table->char('censorship', 1)->default('N');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_reply');
    }
};
