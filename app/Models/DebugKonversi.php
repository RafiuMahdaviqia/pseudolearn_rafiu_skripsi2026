<?php

namespace App\Models;

use App\Core\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class DebugKonversi extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'debug_konversi';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_level',
        'id_soal',
        'id_soal_konversi',
        'id_mahasiswa',
        'id_ujian_konversi',
        'debug',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'debug' => 'array',
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
