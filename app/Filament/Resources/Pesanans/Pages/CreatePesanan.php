<?php

namespace App\Filament\Resources\Pesanans\Pages;

use App\Filament\Resources\Pesanans\PesananResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePesanan extends CreateRecord
{
    protected static string $resource = PesananResource::class;

    protected function afterCreate(): void
    {
        // Item pesanan sudah tersimpan, hitung total harganya.
        $this->getRecord()->hitungUlangTotal();
    }
}
