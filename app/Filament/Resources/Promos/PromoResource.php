<?php

namespace App\Filament\Resources\Promos;

use App\Filament\Resources\Promos\Pages\CreatePromo;
use App\Filament\Resources\Promos\Pages\EditPromo;
use App\Filament\Resources\Promos\Pages\ListPromos;
use App\Filament\Resources\Promos\Schemas\PromoForm;
use App\Filament\Resources\Promos\Tables\PromosTable;
use App\Models\Promo;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Promo';

    protected static ?string $pluralModelLabel = 'Promo';

    protected static ?string $recordTitleAttribute = 'nama_promo';

    // Karyawan hanya boleh mengubah promo yang masih menunggu persetujuan.
    public static function canEdit(Model $record): bool
    {
        return $record->status === 'menunggu';
    }

    public static function form(Schema $schema): Schema
    {
        return PromoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromos::route('/'),
            'create' => CreatePromo::route('/create'),
            'edit' => EditPromo::route('/{record}/edit'),
        ];
    }
}
