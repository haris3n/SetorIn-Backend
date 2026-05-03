<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Nasabah;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopNasabahWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 5 Nasabah Teraktif — Bulan Ini';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $bulan = now()->month;
        $tahun = now()->year;

        return $table
            ->query(
                Nasabah::query()
                    ->with(['pengguna', 'bankSampah'])
                    ->withSum([
                        'transaksi as total_berat_bulan' => function (Builder $q) use ($bulan, $tahun) {
                            $q->whereMonth('tgl_setor', $bulan)
                              ->whereYear('tgl_setor', $tahun)
                              ->where('status', 'selesai');
                        },
                        'transaksi as total_koin_bulan' => function (Builder $q) use ($bulan, $tahun) {
                            $q->whereMonth('tgl_setor', $bulan)
                              ->whereYear('tgl_setor', $tahun)
                              ->where('status', 'selesai');
                        },
                    ], 'total_berat_kg', 'total_koin')
                    ->withCount([
                        'transaksi as total_transaksi_bulan' => function (Builder $q) use ($bulan, $tahun) {
                            $q->whereMonth('tgl_setor', $bulan)
                              ->whereYear('tgl_setor', $tahun)
                              ->where('status', 'selesai');
                        },
                    ])
                    ->having('total_berat_bulan', '>', 0)
                    ->orderByDesc('total_berat_bulan')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('pengguna.nama')
                    ->label('Nama Nasabah')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('bankSampah.nama_bank')
                    ->label('Bank Sampah')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('total_transaksi_bulan')
                    ->label('Transaksi')
                    ->suffix('x')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('total_berat_bulan')
                    ->label('Total Berat')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 1, ',', '.') . ' kg')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('total_koin_bulan')
                    ->label('Koin Diperoleh')
                    ->formatStateUsing(fn ($state) => number_format((int)$state) . ' koin')
                    ->badge()
                    ->color('success'),
            ])
            ->paginated(false);
    }
}
