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
        Schema::create('student_absents', function (Blueprint $table) {
            $table->id();
            $table->date('rec_date');
            $table->string('name', 255);
            $table->integer('nik')->nullable();
            $table->tinyInteger('kehadiran');
            $table->string('note', 255)->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_absents');
    }
};
