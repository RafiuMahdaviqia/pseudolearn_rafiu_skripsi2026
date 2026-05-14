<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Mahasiswa;
use App\Models\Level;
use App\Models\Soal;

class ArsResult extends Model
{
    protected $table = 'ars_result';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = 
    [
        'id',
        'ars_batch',
        'id_mahasiswa',
        'id_level',
        'id_soal',
        'difficulty',
        'pseudo_langkah',
        'pseudo_durasi',
        'pseudo_label',
        'pseudo_score',
        'konversi_langkah',
        'konversi_durasi',
        'konversi_label',
        'konversi_score',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'id_level');
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }
}
