<?php

namespace App\Filament\Petugas\Resources\TransaksiPenyetoranResource\Pages;

use App\Filament\Petugas\Resources\TransaksiPenyetoranResource;
use App\Models\{DetailTransaksiSampah, HargaSampah};
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditTransaksiPenyetoran extends EditRecord
{
    protected static string $resource = TransaksiPenyetoranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol batalkan transaksi (lebih aman daripada delete langsung)
            Actions\Action::make('batalkan')
                ->label('Batalkan Transaksi')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Batalkan Transaksi?')
                ->modalDescription('Transaksi ini akan ditandai sebagai dibatalkan. Aksi ini tidak dapat dibatalkan.')
                ->visible(fn () => $this->record->status !== 'selesai' && $this->record->status !== 'dibatalkan')
                ->action(function () {
                    $this->record->update(['status' => 'dibatalkan']);
                    Notification::make()
                        ->warning()
                        ->title('Transaksi Dibatalkan')
                        ->body('Status transaksi telah diubah menjadi dibatalkan.')
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    /**
     * Override agar saat edit, detail lama dihapus dan dihitung ulang
     * tanpa mengubah koin nasabah (karena edit hanya untuk status 'diproses',
     * belum ada koin yang dikirim).
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($record, $data) {
            $totalBerat = 0;
            $totalKoin  = 0;

            // 1. Hapus semua detail lama, lalu buat ulang
            $record->detail()->delete();

            foreach ($data['detail'] ?? [] as $item) {
                $harga         = HargaSampah::findOrFail($item['id_harga_sampah']);
                $subtotal      = $harga->harga_per_kg * $item['berat_kg'];
                $perolehanKoin = (int)($subtotal / 100);

                DetailTransaksiSampah::create([
                    'id_transaksi'    => $record->id,
                    'id_harga_sampah' => $item['id_harga_sampah'],
                    'berat_kg'        => $item['berat_kg'],
                    'subtotal'        => $subtotal,
                ]);

                $totalBerat += $item['berat_kg'];
                $totalKoin  += $perolehanKoin;
            }

            // 2. Update header transaksi dengan totalan baru & data lainnya
            $record->update([
                'id_nasabah'     => $data['id_nasabah'],
                'catatan'        => $data['catatan'] ?? null,
                'total_berat_kg' => $totalBerat,
                'total_koin'     => $totalKoin,
                // Status tetap 'diproses', tidak diubah saat edit
            ]);

            return $record;
        });
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Transaksi Berhasil Diperbarui')
            ->body('Data transaksi telah dikoreksi. Total berat dan koin dihitung ulang.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
