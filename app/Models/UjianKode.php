<?php

namespace App\Models;

use App\Core\BaseModel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UjianKode extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table      = 'ujian_kode';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'id_level',
        'id_bank_soal_konversi',
        'id_mahasiswa',
        'jawaban',
        'output',
        'nilai',
        'waktu',
    ];

    protected $casts = [
        'nilai' => 'integer',
        'waktu' => 'integer',
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

    public function bankSoalKonversi()
    {
        return $this->belongsTo(BankSoalKonversi::class, 'id_bank_soal_konversi');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'id_level');
    }
}
