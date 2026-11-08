<?php

namespace App\Filament\Resources\Varians\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VarianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('produk_id')
                    ->label('Produk')
                    ->relationship('produk', 'nama_produk')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('warna')
                    ->required()
                    ->maxLength(50),
                TextInput::make('ukuran')
                    ->required()
                    ->maxLength(30),
                TextInput::make('stok')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                DatePicker::make('tanggal_restock')
                    ->label('Tanggal restock'),
            ]);
    }
}
