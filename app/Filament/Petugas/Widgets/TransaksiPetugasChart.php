<?php

namespace App\Filament\Petugas\Widgets;

use App\Models\TransaksiPenyetoran;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiPetugasChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Berat Setoran — Bank Sampah Saya (7 Hari)';

    protected static ?string $maxHeight = '280px';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $petugas = Auth::user()->petugas;
        $bankId  = $petugas?->id_bank_sampah;

        $beratPerHari = TransaksiPenyetoran::where('status', 'selesai')
            ->where('id_bank_sampah', $bankId)
            ->where('tgl_setor', '>=', now()->subDays(6)->startOfDay())
            ->select(
                DB::raw('DATE(tgl_setor) as date'),
                DB::raw('SUM(total_berat_kg) as aggregate')
            )
            ->groupBy('date')
            ->pluck('aggregate', 'date');

        $koinPerHari = TransaksiPenyetoran::where('status', 'selesai')
            ->where('id_bank_sampah', $bankId)
            ->where('tgl_setor', '>=', now()->subDays(6)->startOfDay())
            ->select(
                DB::raw('DATE(tgl_setor) as date'),
                DB::raw('SUM(total_koin) as aggregate')
            )
            ->groupBy('date')
            ->pluck('aggregate', 'date');

        $data       = [];
        $dataKoin   = [];
        $labels     = [];

        for ($i = 6; $i >= 0; $i--) {
            $date     = now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->translatedFormat('d M');
            $data[]   = round((float) $beratPerHari->get($date, 0), 2);
            $dataKoin[] = (int) $koinPerHari->get($date, 0);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Berat (kg)',
                    'data'            => $data,
                    'fill'            => 'start',
                    'tension'         => 0.4,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor'     => '#22c55e',
                    'borderWidth'     => 3,
                    'pointBackgroundColor' => '#22c55e',
                    'yAxisID'         => 'y',
                ],
                [
                    'label'           => 'Koin Diberikan',
                    'data'            => $dataKoin,
                    'fill'            => false,
                    'tension'         => 0.4,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'borderColor'     => '#f59e0b',
                    'borderWidth'     => 2,
                    'borderDash'      => [5, 5],
                    'pointBackgroundColor' => '#f59e0b',
                    'yAxisID'         => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type'     => 'linear',
                    'display'  => true,
                    'position' => 'left',
                    'title'    => ['display' => true, 'text' => 'Berat (kg)'],
                ],
                'y1' => [
                    'type'     => 'linear',
                    'display'  => true,
                    'position' => 'right',
                    'title'    => ['display' => true, 'text' => 'Koin'],
                    'grid'     => ['drawOnChartArea' => false],
                ],
            ],
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return null;
    }
}
