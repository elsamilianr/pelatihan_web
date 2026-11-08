<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Varian extends Model
{
    protected $guarded = [];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    // Teks untuk dropdown, mis. "Helm X - Hitam / L (stok 10)"
    public function getLabelLengkapAttribute(): string
    {
        return ($this->produk?->nama_produk ?? '-') . " - {$this->warna} / {$this->ukuran} (stok {$this->stok})";
    }
}
