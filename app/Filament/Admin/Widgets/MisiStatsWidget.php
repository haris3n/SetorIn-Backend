<?php

namespace App\Filament\Admin\Widgets;

use App\Models\{Misi, KlaimMisi, Nasabah, User};
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MisiStatsWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected function getPollingInterval(): ?string
    {
        return null;
    }

    protected function getStats(): array
    {
        $bulan = now()->month;
        $tahun = now()->year;

        // Total misi yang sedang aktif
        $misiAktif = Misi::where('status_misi', 'aktif')
            ->where('tgl_mulai', '<=', now())
            ->where('tgl_selesai', '>=', now())
            ->count();

        // Total klaim misi bulan ini
        $klaimBulanIni = KlaimMisi::whereMonth('tgl_klaim', $bulan)
            ->whereYear('tgl_klaim', $tahun)
            ->where('status_klaim', 'selesai')
            ->count();

        // Total koin dari misi bulan ini
        $koinDariMisi = KlaimMisi::whereMonth('tgl_klaim', $bulan)
            ->whereYear('tgl_klaim', $tahun)
            ->where('status_klaim', 'selesai')
            ->sum('koin_diterima');

        // Persentase nasabah yang pernah klaim misi (all-time)
        $totalNasabah     = Nasabah::count();
        $nasabahYangKlaim = KlaimMisi::distinct('id_pengguna')->count('id_pengguna');
        $persen           = $totalNasabah > 0 ? round(($nasabahYangKlaim / $totalNasabah) * 100, 1) : 0;

        return [
            Stat::make('Misi Aktif Saat Ini', $misiAktif)
                ->description('Misi yang sedang berjalan')
                ->descriptionIcon('heroicon-m-flag')
                ->color('primary'),

            Stat::make('Klaim Misi Bulan Ini', $klaimBulanIni)
                ->description('Total klaim berhasil')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Koin dari Misi Bulan Ini', number_format($koinDariMisi))
                ->description('Total koin reward misi')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Partisipasi Nasabah', $persen . '%')
                ->description("{$nasabahYangKlaim} dari {$totalNasabah} nasabah pernah klaim misi")
                ->descriptionIcon('heroicon-m-user-group')
                ->color($persen >= 50 ? 'success' : ($persen >= 25 ? 'warning' : 'danger')),
        ];
    }
}
