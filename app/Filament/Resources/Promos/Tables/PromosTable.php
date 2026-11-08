<?php

namespace App\Filament\Resources\Promos\Tables;

use App\Models\Promo;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PromosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_promo')
                    ->label('Nama promo')
                    ->searchable(),
                TextColumn::make('diskon')
                    ->formatStateUsing(fn ($state, Promo $record): string => $record->jenis_diskon === 'persen'
                        ? "{$state}%"
                        : 'Rp ' . number_format((int) $state, 0, ',', '.')),
                TextColumn::make('cakupan')
                    ->label('Berlaku untuk')
                    ->badge(),
                TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d M Y'),
                TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->date('d M Y'),
                // Status diputuskan oleh pemilik; karyawan hanya melihatnya.
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'menunggu' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('tanggal_mulai', 'desc')
            ->recordActions([
                // Hanya promo yang masih "menunggu" yang bisa diubah.
                EditAction::make()
                    ->visible(fn (Promo $record): bool => $record->status === 'menunggu'),
            ]);
    }
}
