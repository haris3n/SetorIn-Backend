<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected static string $view = 'filament.admin.login';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Masuk sebagai Admin';
    }

    public function getSubHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'Kelola sistem Setor.in dengan penuh tanggung jawab.';
    }
}
