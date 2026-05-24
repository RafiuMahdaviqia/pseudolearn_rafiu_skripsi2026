<?php

namespace App\Models;

use App\Core\BaseModel;
use App\Enums\SoalDifficulty;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Soal extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

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
        'difficulty',
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

    public function konversi() : HasMany
    {
        return $this->hasMany(Konversi::class, 'id_soal');
    }

    public function ujian() : HasMany
    {
        return $this->hasMany(Ujian::class, 'id_soal');
    }

    public function labelSkor() : HasMany
    {
        return $this->hasMany(LabelSkor::class, 'id_soal');
    }

    public function level() : BelongsTo
    {
        return $this->belongsTo(Level::class, 'id_level');
    }

    public static function activeStatusValues(): array
    {
        return [self::STATUS_ACTIVE, '1', 'active'];
    }

    public static function hasDifficultyColumn(): bool
    {
        return Schema::hasColumn((new static())->getTable(), 'difficulty');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', self::activeStatusValues());
    }
}
