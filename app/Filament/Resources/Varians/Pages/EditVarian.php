<?php

namespace App\Filament\Resources\Varians\Pages;

use App\Filament\Resources\Varians\VarianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVarian extends EditRecord
{
    protected static string $resource = VarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
