<?php

namespace App\Models;

use App\Core\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class UjianKonversi extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'ujian_konversi';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_level',
        'id_soal_konversi',
        'id_mahasiswa',
        'jawaban',
        'output',
        'waktu',
        'nilai',
    ];

    protected $casts = [
        'jawaban' => 'array',
        'nilai' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
