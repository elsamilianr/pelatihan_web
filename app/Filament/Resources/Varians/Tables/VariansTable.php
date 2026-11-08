<?php

namespace App\Filament\Resources\Varians\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('produk.nama_produk')
                    ->label('Produk')
                    ->searchable(),
                TextColumn::make('warna')
                    ->searchable(),
                TextColumn::make('ukuran'),
                TextColumn::make('stok')
                    ->sortable(),
                TextColumn::make('harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('tanggal_restock')
                    ->label('Restock')
                    ->date('d M Y'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
