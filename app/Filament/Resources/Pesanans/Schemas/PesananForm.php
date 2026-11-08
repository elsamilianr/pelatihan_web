<?php

namespace App\Filament\Resources\Pesanans\Schemas;

use App\Models\Promo;
use App\Models\Varian;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PesananForm
{
    private static function hitungPotonganState(?int $promoId, mixed $items): int
    {
        if (! $promoId || ! is_array($items)) {
            return 0;
        }

        $promo = Promo::with('produks')
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', today())
            ->whereDate('tanggal_selesai', '>=', today())
            ->find($promoId);

        if (! $promo) {
            return 0;
        }

        $produkPromoIds = $promo->produks->pluck('id')->all();
        $subtotalBerlaku = 0;

        foreach ($items as $item) {
            $varianId = $item['varian_id'] ?? null;
            $jumlah = max(0, (int) ($item['jumlah'] ?? 0));

            if (! $varianId || $jumlah < 1) {
                continue;
            }

            $varian = Varian::find($varianId);

            if (! $varian) {
                continue;
            }

            $berlaku = $promo->cakupan === 'semua'
                || (
                    $promo->cakupan === 'produk'
                    && in_array($varian->produk_id, $produkPromoIds)
                );

            if ($berlaku) {
                $subtotalBerlaku += (int) $varian->harga * $jumlah;
            }
        }

        if ($subtotalBerlaku < 1) {
            return 0;
        }

        $potongan = $promo->jenis_diskon === 'persen'
            ? (int) round($subtotalBerlaku * ((float) $promo->diskon / 100))
            : (int) $promo->diskon;

        return min($potongan, $subtotalBerlaku);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('kode_pesanan')
                ->label('Kode pesanan')
                ->placeholder('Dibuat otomatis')
                ->disabled()
                ->dehydrated(false),

            Select::make('jenis_transaksi')
                ->label('Jenis transaksi')
                ->options([
                    'toko' => 'Toko (langsung)',
                ])
                ->default('toko')
                ->required(),

            TextInput::make('nama_pelanggan')
                ->label('Nama pelanggan')
                ->maxLength(100),

            Select::make('user_id')
                ->label('Akun pembeli (opsional)')
                ->relationship(
                    'user',
                    'nama',
                    fn (Builder $query) => $query->where('role', 'pembeli')
                )
                ->searchable()
                ->preload(),

            Select::make('promo_id')
                ->label('Promo')
                ->relationship(
                    'promo',
                    'nama_promo',
                    fn (Builder $query) => $query
                        ->where('status', 'disetujui')
                        ->whereDate('tanggal_mulai', '<=', today())
                        ->whereDate('tanggal_selesai', '>=', today())
                )
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                    $set(
                        'potongan',
                        self::hitungPotonganState(
                            $state ? (int) $state : null,
                            $get('detailPesanans')
                        )
                    );
                })
                ->helperText('Hanya promo yang disetujui dan masih berlaku.'),

            TextInput::make('potongan')
                ->label('Potongan promo')
                ->numeric()
                ->prefix('Rp')
                ->default(0)
                ->disabled()
                ->dehydrated()
                ->helperText('Otomatis dihitung berdasarkan promo.'),

            Select::make('status_pesanan')
                ->label('Status pesanan')
                ->options([
                    'menunggu_pembayaran' => 'Menunggu pembayaran',
                    'diproses' => 'Diproses',
                    'dikirim' => 'Dikirim',
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ])
                ->default('selesai')
                ->required()
                ->live(),

            TextInput::make('alasan_batal')
                ->label('Catatan pembatalan')
                ->placeholder('Tulis alasan pesanan dibatalkan')
                ->maxLength(255)
                ->visible(fn (Get $get): bool => $get('status_pesanan') === 'dibatalkan')
                ->required(fn (Get $get): bool => $get('status_pesanan') === 'dibatalkan'),

            TextInput::make('total_harga')
                ->label('Total harga')
                ->prefix('Rp')
                ->numeric()
                ->disabled()
                ->dehydrated(false)
                ->helperText('Dihitung otomatis.'),

            Hidden::make('kasir_id')
                ->default(fn () => auth()->id()),

            Repeater::make('detailPesanans')
                ->label('Item pesanan')
                ->relationship('detailPesanans')
                ->schema([
                    Select::make('varian_id')
                        ->label('Produk / varian')
                        ->options(
                            fn (): array => Varian::with('produk')
                                ->get()
                                ->mapWithKeys(
                                    fn (Varian $varian): array => [
                                        $varian->id => $varian->label_lengkap,
                                    ]
                                )
                                ->all()
                        )
                        ->searchable()
                        ->required()
                        ->live()
                        ->distinct()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                            $varian = $state ? Varian::find($state) : null;

                            $set('harga', $varian?->harga ?? 0);

                            $set(
                                '../../potongan',
                                self::hitungPotonganState(
                                    $get('../../promo_id')
                                        ? (int) $get('../../promo_id')
                                        : null,
                                    $get('../../detailPesanans')
                                )
                            );
                        }),

                    TextInput::make('jumlah')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                            $set(
                                '../../potongan',
                                self::hitungPotonganState(
                                    $get('../../promo_id')
                                        ? (int) $get('../../promo_id')
                                        : null,
                                    $get('../../detailPesanans')
                                )
                            );
                        }),

                    TextInput::make('harga')
                        ->label('Harga satuan')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->helperText('Otomatis mengikuti harga varian.'),
                ])
                ->columns(3)
                ->minItems(1)
                ->addActionLabel('Tambah item')
                ->columnSpanFull(),
        ]);
    }
}
