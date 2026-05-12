<?php

namespace App\Filament\Petugas\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected static string $view = 'filament.petugas.login';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Selamat Datang, Petugas';
    }

    public function getSubHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'Masuk untuk memproses transaksi penyetoran sampah.';
    }
}
