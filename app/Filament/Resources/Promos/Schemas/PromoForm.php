<?php

namespace App\Filament\Resources\Promos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PromoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_promo')
                    ->label('Nama promo')
                    ->required()
                    ->maxLength(100),
                Select::make('jenis_diskon')
                    ->label('Jenis diskon')
                    ->options([
                        'persen' => 'Persen (%)',
                        'nominal' => 'Nominal (Rp)',
                    ])
                    ->default('persen')
                    ->required()
                    ->live(),
                TextInput::make('diskon')
                    ->label('Besar diskon')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(fn (Get $get): ?int => $get('jenis_diskon') === 'persen' ? 100 : null)
                    ->prefix(fn (Get $get): ?string => $get('jenis_diskon') === 'nominal' ? 'Rp' : null)
                    ->suffix(fn (Get $get): ?string => $get('jenis_diskon') === 'persen' ? '%' : null)
                    ->required(),
                Select::make('cakupan')
                    ->label('Berlaku untuk')
                    ->options([
                        'semua' => 'Semua produk',
                        'produk' => 'Produk tertentu',
                    ])
                    ->default('semua')
                    ->required()
                    ->live(),
                Select::make('produks')
                    ->label('Pilih produk')
                    ->relationship('produks', 'nama_produk')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->visible(fn (Get $get): bool => $get('cakupan') === 'produk'),
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal mulai')
                    ->required(),
                DatePicker::make('tanggal_selesai')
                    ->label('Tanggal selesai')
                    ->afterOrEqual('tanggal_mulai')
                    ->required(),
                Hidden::make('dibuat_oleh')
                    ->default(fn () => auth()->id()),
            ]);
    }
}
