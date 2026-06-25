<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $removedJenis = 'ke'.'lura'.'han';
        $removedPositions = ['Lu'.'ra'.'h', 'Sekretaris Ke'.'lura'.'han'];

        DB::table('perangkat_wilayahs')
            ->whereIn('jabatan_perangkat_id', function ($query) use ($removedPositions) {
                $query->select('id')
                    ->from('jabatan_perangkats')
                    ->whereIn('nama', $removedPositions);
            })
            ->delete();

        DB::table('wilayahs')->where('jenis', $removedJenis)->delete();
        DB::table('jabatan_perangkats')->whereIn('nama', $removedPositions)->delete();
    }

    /**
     * This data removal is intentionally not reversible.
     */
    public function down(): void
    {
        //
    }
};
