<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['tanggal_bayar' => 'datetime'];
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
