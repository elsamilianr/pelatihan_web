<?php

namespace App\Filament\Resources\Branches\Schemas;

use App\Forms\Components\MapPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')->required(),
                Textarea::make('alamat')->required()->columnSpanFull(),
                TextInput::make('telepon')->tel(),

                MapPicker::make('lokasi')
                    ->label('Pilih lokasi di peta')
                    ->height(380)
                    ->defaultLocation(-6.2, 106.82, 12),

                TextInput::make('latitude')
                    ->required()->numeric()
                    ->minValue(-90)->maxValue(90)
                    ->helperText('Contoh: -6.1754'),
                TextInput::make('longitude')
                    ->required()->numeric()
                    ->minValue(-180)->maxValue(180)
                    ->helperText('Contoh: 106.8272'),
            ]);
    }
}
