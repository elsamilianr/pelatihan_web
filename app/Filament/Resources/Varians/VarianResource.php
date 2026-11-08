<?php

namespace App\Filament\Resources\Varians;

use App\Filament\Resources\Varians\Pages\CreateVarian;
use App\Filament\Resources\Varians\Pages\EditVarian;
use App\Filament\Resources\Varians\Pages\ListVarians;
use App\Filament\Resources\Varians\Schemas\VarianForm;
use App\Filament\Resources\Varians\Tables\VariansTable;
use App\Models\Varian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VarianResource extends Resource
{
    protected static ?string $model = Varian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Varian';

    protected static ?string $pluralModelLabel = 'Varian';

    protected static ?string $recordTitleAttribute = 'warna';

    public static function form(Schema $schema): Schema
    {
        return VarianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VariansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVarians::route('/'),
            'create' => CreateVarian::route('/create'),
            'edit' => EditVarian::route('/{record}/edit'),
        ];
    }
}
