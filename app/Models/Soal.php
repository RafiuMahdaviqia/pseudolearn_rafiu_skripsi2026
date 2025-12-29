<?php

namespace App\Models;

use App\Core\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Soal extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'soal';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_level',
        'judul',
        'soal',
        'kunci_tipe_data',
        'kunci_algoritma',
        'order',
        'status',
    ];

    protected $casts = [
        'kunci_tipe_data' => 'array',
        'kunci_algoritma' => 'array',
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
