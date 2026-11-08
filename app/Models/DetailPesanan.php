<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $guarded = [];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function varian()
    {
        return $this->belongsTo(Varian::class);
    }
}
