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
        Schema::create('perangkat_wilayahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jabatan_perangkat_id')->constrained()->restrictOnDelete();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('nomor_hp', 30)->nullable();
            $table->string('email')->nullable();
            $table->date('mulai_menjabat')->nullable();
            $table->date('akhir_menjabat')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perangkat_wilayahs');
    }
};
