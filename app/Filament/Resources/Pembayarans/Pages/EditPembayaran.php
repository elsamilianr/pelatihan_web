<?php

namespace App\Filament\Resources\Pembayarans\Pages;

use App\Filament\Resources\Pembayarans\PembayaranResource;
use Filament\Resources\Pages\EditRecord;

class EditPembayaran extends EditRecord
{
    protected static string $resource = PembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
