<?php

namespace App\Filament\Resources\Pembayarans\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PembayaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pesanan.kode_pesanan')
                    ->label('Pesanan')
                    ->searchable(),
                TextColumn::make('metode')
                    ->badge(),
                TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('tanggal_bayar')
                    ->label('Tanggal bayar')
                    ->dateTime('d M Y H:i'),
                ImageColumn::make('bukti_pembayaran')
                    ->label('Bukti')
                    ->disk('public'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
