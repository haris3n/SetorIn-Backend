<?php

namespace App\Filament\Admin\Widgets;

use App\Models\TransaksiPenyetoran;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BeratSampahChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Berat Sampah Terkumpul (7 Hari Terakhir)';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data   = [];
        $labels = [];

        $beratPerHari = TransaksiPenyetoran::where('status', 'selesai')
            ->where('tgl_setor', '>=', now()->subDays(6)->startOfDay())
            ->select(
                DB::raw('DATE(tgl_setor) as date'),
                DB::raw('SUM(total_berat_kg) as aggregate')
            )
            ->groupBy('date')
            ->pluck('aggregate', 'date');

        for ($i = 6; $i >= 0; $i--) {
            $date     = now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->translatedFormat('d M');
            $data[]   = round((float) $beratPerHari->get($date, 0), 2);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Berat Terkumpul (kg)',
                    'data'            => $data,
                    'fill'            => 'start',
                    'tension'         => 0.4,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'borderColor'     => '#f59e0b',
                    'borderWidth'     => 3,
                    'pointBackgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getPollingInterval(): ?string
    {
        return null;
    }
}
