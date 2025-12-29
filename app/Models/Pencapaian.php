<?php

namespace App\Models;

use App\Core\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Pencapaian extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pencapaian';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_mahasiswa',
        'id_level',
        'id_soal',
        'id_soal_konversi',
        'category', // 'leaderboard','badge','soal','konversi'
        'img',
        'name',
        'desc',
        'progress',
        'max_progress',
        'status', // 0: belum hak, 1: not claimed, 2: claimed
        'heart',
        'date_claimed',
    ];

    protected $casts = [
        'progress' => 'integer',
        'max_progress' => 'integer',
        'status' => 'integer',
        'heart' => 'integer',
        'date_claimed' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
