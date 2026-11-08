<?php

namespace App\Filament\Resources\Pesanans\Pages;

use App\Filament\Resources\Pesanans\PesananResource;
use Filament\Resources\Pages\EditRecord;

class EditPesanan extends EditRecord
{
    protected static string $resource = PesananResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status_pesanan'] ?? null) === 'dibatalkan') {
            // Catat siapa dan kapan pesanan dibatalkan (hanya saat pertama kali dibatalkan).
            if (blank($this->getRecord()->dibatalkan_pada)) {
                $data['dibatalkan_oleh'] = auth()->id();
                $data['dibatalkan_pada'] = now();
            }
        } else {
            // Status dikembalikan: hapus data pembatalan.
            $data['alasan_batal'] = null;
            $data['dibatalkan_oleh'] = null;
            $data['dibatalkan_pada'] = null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Item atau potongan bisa berubah, jadi hitung ulang total.
        $this->getRecord()->hitungUlangTotal();
        $this->refreshFormData([
            'total_harga',
            'potongan',
        ]);
    }
}