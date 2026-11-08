<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable(),
                TextColumn::make('latitude')
                    ->label('Latitude')
                    ->searchable(),
                TextColumn::make('longitude')
                    ->label('Longitude')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->formatStateUsing(fn ($state) => $state?->locale('id')->translatedFormat('d M Y'))
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                //
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
