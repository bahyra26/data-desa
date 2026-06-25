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
        Schema::table('perangkat_wilayahs', function (Blueprint $table) {
            if (! Schema::hasColumn('perangkat_wilayahs', 'foto')) {
                $table->string('foto')->nullable()->after('nama');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perangkat_wilayahs', function (Blueprint $table) {
            if (Schema::hasColumn('perangkat_wilayahs', 'foto')) {
                $table->dropColumn('foto');
            }
        });
    }
};
