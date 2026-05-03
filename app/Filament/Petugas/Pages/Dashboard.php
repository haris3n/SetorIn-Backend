<?php

namespace App\Filament\Petugas\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title           = 'Dashboard Petugas';
    protected static ?int    $navigationSort  = -2;

    public function getWidgets(): array
    {
        return [
            // Baris 1 — Statistik Harian & Bulanan
            \App\Filament\Petugas\Widgets\PetugasStats::class,

            // Baris 2 — Grafik Tren Berat & Koin
            \App\Filament\Petugas\Widgets\TransaksiPetugasChart::class,
        ];
    }
}
