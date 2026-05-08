<?php

namespace App\Filament\Petugas\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Widget;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Petugas Dashboard';

    public function getWidgets(): array
    {
        return [
            Widget::live(
                // ... other widget configurations ...
            ),
        ];
    }
}
