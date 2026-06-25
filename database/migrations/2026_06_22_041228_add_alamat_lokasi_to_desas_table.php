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
        Schema::table('desas', function (Blueprint $table) {
            if (! Schema::hasColumn('desas', 'alamat_kantor')) {
                $table->text('alamat_kantor')->nullable()->after('wilayah_id');
            }

            if (! Schema::hasColumn('desas', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('luas_wilayah');
            }

            if (! Schema::hasColumn('desas', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desas', function (Blueprint $table) {
            if (Schema::hasColumn('desas', 'longitude')) {
                $table->dropColumn('longitude');
            }

            if (Schema::hasColumn('desas', 'latitude')) {
                $table->dropColumn('latitude');
            }

            if (Schema::hasColumn('desas', 'alamat_kantor')) {
                $table->dropColumn('alamat_kantor');
            }
        });
    }
};
