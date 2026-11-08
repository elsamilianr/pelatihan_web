<?php

namespace App\Filament\Resources\Produks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_produk')
                    ->label('Nama produk')
                    ->required()
                    ->maxLength(150),
                Textarea::make('deskripsi')
                    ->label('Deskripsi'),
                FileUpload::make('foto')
                    ->label('Foto')
                    ->image()
                    ->disk('public')
                    ->directory('produk'),
            ]);
    }
}
