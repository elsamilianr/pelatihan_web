<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pesanan extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Pesanan $pesanan) {
            if (blank($pesanan->kode_pesanan)) {
                $pesanan->kode_pesanan = 'PSN-'
                    . now()->format('Ymd')
                    . '-'
                    . strtoupper(Str::random(4));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function sinkronkanHargaDanPotongan(): void
    {
        $this->loadMissing([
            'detailPesanans.varian',
            'promo.produks',
        ]);

        $subtotal = 0;
        $subtotalBerlakuPromo = 0;

        $produkPromoIds = $this->promo?->produks
            ?->pluck('id')
            ->all() ?? [];

        foreach ($this->detailPesanans as $detail) {
            if (! $detail->varian) {
                continue;
            }

            // Harga selalu diambil dari database varian.
            $harga = (int) $detail->varian->harga;
            $jumlah = max(0, (int) $detail->jumlah);

            if ((int) $detail->harga !== $harga) {
                $detail->harga = $harga;
                $detail->saveQuietly();
            }

            $nilaiItem = $harga * $jumlah;
            $subtotal += $nilaiItem;

            $promoBerlakuUntukItem = $this->promo
                && $this->promo->status === 'disetujui'
                && $this->promo->tanggal_mulai
                && $this->promo->tanggal_selesai
                && today()->between(
                    $this->promo->tanggal_mulai,
                    $this->promo->tanggal_selesai
                )
                && (
                    $this->promo->cakupan === 'semua'
                    || (
                        $this->promo->cakupan === 'produk'
                        && in_array(
                            $detail->varian->produk_id,
                            $produkPromoIds
                        )
                    )
                );

            if ($promoBerlakuUntukItem) {
                $subtotalBerlakuPromo += $nilaiItem;
            }
        }

        $potongan = 0;

        if ($this->promo && $subtotalBerlakuPromo > 0) {
            $potongan = $this->promo->jenis_diskon === 'persen'
                ? (int) round(
                    $subtotalBerlakuPromo
                    * ((float) $this->promo->diskon / 100)
                )
                : (int) $this->promo->diskon;

            $potongan = min($potongan, $subtotalBerlakuPromo);
        }

        $this->forceFill([
            'potongan' => $potongan,
            'total_harga' => max(0, $subtotal - $potongan),
        ])->saveQuietly();
    }

    public function hitungUlangTotal(): void
    {
        $this->sinkronkanHargaDanPotongan();
    }
}
