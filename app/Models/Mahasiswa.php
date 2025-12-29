<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Core\BaseModel;

class Mahasiswa extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_user',
        'id_kelas',
        'nim',
        'name',
        'jenis_kelamin',
        'open_panduan', // 0 : belum dibuka, 1 : sudah dibuka
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
