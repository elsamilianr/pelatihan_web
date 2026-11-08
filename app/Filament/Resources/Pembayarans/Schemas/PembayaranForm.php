<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use App\Models\Pesanan;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PembayaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pesanan_id')
                    ->label('Pesanan')
                    ->relationship('pesanan', 'kode_pesanan')
                    ->getOptionLabelFromRecordUsing(fn (Pesanan $record): string => $record->kode_pesanan ?? "Pesanan #{$record->id}")
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('metode')
                    ->label('Metode pembayaran')
                    ->required()
                    ->maxLength(30)
                    ->datalist(['tunai', 'transfer', 'qris']),
                Select::make('status_pembayaran')
                    ->label('Status pembayaran')
                    ->required()
                    ->default('lunas')
                    ->options([
                        'belum_dibayar',
                        'menunggu_verifikasi',
                        'lunas',
                        'ditolak',
                    ]),
                DateTimePicker::make('tanggal_bayar')
                    ->label('Tanggal bayar')
                    ->default(now()),
                FileUpload::make('bukti_pembayaran')
                    ->label('Bukti pembayaran (opsional)')
                    ->image()
                    ->disk('public')
                    ->directory('bukti-pembayaran'),
            ]);
    }
}
