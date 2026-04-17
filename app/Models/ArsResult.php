<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsResult extends Model
{
    protected $fillable = [
        'id_mahasiswa',
        'id_soal',
        'cluster',
        'jenis_soal',
        'difficulty_sekarang',
        'rekomendasi_difficulty'
    ];
    protected $casts = [
        'difficulty_sekarang' => 'string',
        'rekomendasi_difficulty' => 'string',
    ];
}
