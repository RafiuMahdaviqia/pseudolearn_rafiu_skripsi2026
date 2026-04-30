<?php

namespace App\Models;

use App\Core\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LabelSkor extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'label_skor';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'id_level',
        'id_soal',
        'id_mahasiswa',
        'label',
        'skor',
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function soal() : BelongsTo
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function level() : BelongsTo
    {
        return $this->belongsTo(Level::class, 'id_level');
    }

    public function mahasiswa() : BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }
}
