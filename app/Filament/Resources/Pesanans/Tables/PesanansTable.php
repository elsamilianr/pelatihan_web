<?php

namespace App\Filament\Resources\Pesanans\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PesanansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_pesanan')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('user.nama')
                    ->label('Pembeli')
                    ->placeholder('-'),
                TextColumn::make('nama_pelanggan')
                    ->label('Pelanggan toko')
                    ->placeholder('-'),
                TextColumn::make('jenis_transaksi')
                    ->label('Jenis')
                    ->badge(),
                TextColumn::make('total_harga')
                    ->label('Total')
                    ->money('IDR', locale: 'id'),
                TextColumn::make('status_pesanan')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
