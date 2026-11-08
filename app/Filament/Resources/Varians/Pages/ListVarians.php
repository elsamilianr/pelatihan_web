<?php

namespace App\Filament\Resources\Varians\Pages;

use App\Filament\Resources\Varians\VarianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVarians extends ListRecords
{
    protected static string $resource = VarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
