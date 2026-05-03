<?php

namespace App\Filament\Admin\Widgets;

use App\Models\BankSampah;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BankSampahStatsWidget extends BaseWidget
{
    protected static ?string $heading = 'Performa Bank Sampah — Bulan Ini';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $bulan = now()->month;
        $tahun = now()->year;

        return $table
            ->query(
                BankSampah::query()
                    ->where('status', 'aktif')
                    ->withCount([
                        'nasabah',
                        'transaksi as total_transaksi_bulan' => function (Builder $query) use ($bulan, $tahun) {
                            $query->whereMonth('tgl_setor', $bulan)
                                  ->whereYear('tgl_setor', $tahun)
                                  ->where('status', 'selesai');
                        },
                    ])
                    ->withSum([
                        'transaksi as total_berat_bulan' => function (Builder $query) use ($bulan, $tahun) {
                            $query->whereMonth('tgl_setor', $bulan)
                                  ->whereYear('tgl_setor', $tahun)
                                  ->where('status', 'selesai');
                        },
                        'transaksi as total_koin_bulan' => function (Builder $query) use ($bulan, $tahun) {
                            $query->whereMonth('tgl_setor', $bulan)
                                  ->whereYear('tgl_setor', $tahun)
                                  ->where('status', 'selesai');
                        },
                    ], 'total_berat_kg', 'total_koin')
                    ->orderByDesc('total_berat_bulan')
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_bank')
                    ->label('Bank Sampah')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nasabah_count')
                    ->label('Nasabah')
                    ->suffix(' orang')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_transaksi_bulan')
                    ->label('Transaksi Bulan Ini')
                    ->suffix(' transaksi')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('total_berat_bulan')
                    ->label('Berat Terkumpul')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 1, ',', '.') . ' kg')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('total_koin_bulan')
                    ->label('Total Koin Diberikan')
                    ->formatStateUsing(fn ($state) => number_format((int)$state) . ' koin')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif'    => 'success',
                        'nonaktif' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->paginated(false);
    }
}
