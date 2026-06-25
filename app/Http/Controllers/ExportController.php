<?php

namespace App\Http\Controllers;

use App\Exports\ActivityLogsExport;
use App\Exports\DesaExport;
use App\Exports\PerangkatExport;
use App\Models\Desa;
use App\Models\PerangkatWilayah;
use App\Services\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function desaExcel(Request $request): BinaryFileResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kecamatan_id' => ['nullable', 'integer', 'exists:kecamatans,id'],
        ]);

        ActivityLogger::log(
            $request,
            'export_excel',
            'desa',
            'Export Excel data desa.',
            null,
            null,
            ['filters' => $filters]
        );

        return Excel::download(new DesaExport($filters), 'data-desa-'.now()->format('Ymd-His').'.xlsx');
    }

    public function desaListPdf(Request $request)
    {
        $filters = $this->validateDesaFilters($request);

        ActivityLogger::log(
            $request,
            'export_pdf',
            'desa',
            'Export PDF data desa.',
            null,
            null,
            ['filters' => $filters]
        );

        return Pdf::loadView('exports.desa-list-pdf', [
            'rows' => (new DesaExport($filters))->collection(),
            'tanggalCetak' => now(),
        ])
            ->setPaper('a4', 'landscape')
            ->download('data-desa-'.now()->format('Ymd-His').'.pdf');
    }

    public function perangkatExcel(Request $request): BinaryFileResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kecamatan_id' => ['nullable', 'integer', 'exists:kecamatans,id'],
            'jabatan_perangkat_id' => ['nullable', 'integer', 'exists:jabatan_perangkats,id'],
            'status' => ['nullable', Rule::in(['aktif', 'nonaktif', 'selesai', 'pensiun'])],
            'masa_jabatan' => ['nullable', Rule::in(['aktif', 'hampir_berakhir', 'berakhir', 'belum_mulai', 'tanpa_batas'])],
        ]);

        ActivityLogger::log(
            $request,
            'export_excel',
            'perangkat',
            'Export Excel data perangkat.',
            null,
            null,
            ['filters' => $filters]
        );

        return Excel::download(new PerangkatExport($filters), 'data-perangkat-'.now()->format('Ymd-His').'.xlsx');
    }

    public function perangkatPdf(Request $request)
    {
        $filters = $this->validatePerangkatFilters($request);

        ActivityLogger::log(
            $request,
            'export_pdf',
            'perangkat',
            'Export PDF data perangkat.',
            null,
            null,
            ['filters' => $filters]
        );

        return Pdf::loadView('exports.perangkat-pdf', [
            'rows' => (new PerangkatExport($filters))->collection(),
            'tanggalCetak' => now(),
        ])
            ->setPaper('a4', 'landscape')
            ->download('data-perangkat-'.now()->format('Ymd-His').'.pdf');
    }

    public function activityLogsExcel(Request $request): BinaryFileResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'module' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        ActivityLogger::log(
            $request,
            'export_excel',
            'activity_log',
            'Export Excel audit log.',
            null,
            null,
            ['filters' => $filters]
        );

        return Excel::download(new ActivityLogsExport($filters), 'audit-log-'.now()->format('Ymd-His').'.xlsx');
    }

    public function activityLogsPdf(Request $request)
    {
        $filters = $this->validateActivityLogFilters($request);

        ActivityLogger::log(
            $request,
            'export_pdf',
            'activity_log',
            'Export PDF audit log.',
            null,
            null,
            ['filters' => $filters]
        );

        return Pdf::loadView('exports.activity-logs-pdf', [
            'rows' => (new ActivityLogsExport($filters))->collection(),
            'tanggalCetak' => now(),
        ])
            ->setPaper('a4', 'landscape')
            ->download('audit-log-'.now()->format('Ymd-His').'.pdf');
    }

    public function desaPdf(Request $request, Desa $desa)
    {
        $desa->load('wilayah.kecamatan');

        $perangkatAktif = PerangkatWilayah::with('jabatanPerangkat')
            ->where('wilayah_id', $desa->wilayah_id)
            ->where('status', 'aktif')
            ->join('jabatan_perangkats', 'perangkat_wilayahs.jabatan_perangkat_id', '=', 'jabatan_perangkats.id')
            ->orderBy('jabatan_perangkats.level_urutan')
            ->orderBy('perangkat_wilayahs.nama')
            ->select('perangkat_wilayahs.*')
            ->get();

        $riwayatPerangkat = PerangkatWilayah::with('jabatanPerangkat')
            ->where('wilayah_id', $desa->wilayah_id)
            ->whereIn('status', ['nonaktif', 'selesai', 'pensiun'])
            ->orderByDesc('akhir_menjabat')
            ->orderByDesc('updated_at')
            ->get();

        ActivityLogger::log(
            $request,
            'export_pdf',
            'desa',
            'Export PDF detail desa '.$desa->wilayah->nama.'.',
            $desa
        );

        return Pdf::loadView('exports.desa-pdf', [
            'desa' => $desa,
            'perangkatAktif' => $perangkatAktif,
            'riwayatPerangkat' => $riwayatPerangkat,
            'tanggalCetak' => now(),
        ])->download('detail-desa-'.$desa->id.'-'.now()->format('Ymd-His').'.pdf');
    }

    private function validateDesaFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kecamatan_id' => ['nullable', 'integer', 'exists:kecamatans,id'],
        ]);
    }

    private function validatePerangkatFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kecamatan_id' => ['nullable', 'integer', 'exists:kecamatans,id'],
            'jabatan_perangkat_id' => ['nullable', 'integer', 'exists:jabatan_perangkats,id'],
            'status' => ['nullable', Rule::in(['aktif', 'nonaktif', 'selesai', 'pensiun'])],
            'masa_jabatan' => ['nullable', Rule::in(['aktif', 'hampir_berakhir', 'berakhir', 'belum_mulai', 'tanpa_batas'])],
        ]);
    }

    private function validateActivityLogFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'module' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
    }
}
