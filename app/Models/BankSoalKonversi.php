<?php

namespace App\Models;

use App\Core\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class BankSoalKonversi extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'bank_soal_konversi';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_level',
        'id_soal',
        'order',
        'jawaban',
        'output',
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

    public static function parseJawabanLines(?string $jawaban): array
    {
        if ($jawaban === null) {
            return [];
        }

        $normalized = str_replace(["\r\n", "\r"], "\n", trim($jawaban));
        if ($normalized === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (string $line): string => trim($line),
            explode("\n", $normalized)
        ), static fn (string $line): string => $line !== ''));
    }

    public static function linesMatch(?string $expected, ?string $actual): bool
    {
        $expectedLine = trim((string) $expected);
        $actualLine = trim((string) $actual);

        if ($expectedLine === '' || $actualLine === '') {
            return false;
        }

        // Normalize internal whitespace (multiple spaces, tabs, NBSP) to single space
        $normalize = static fn(string $s): string => preg_replace('/\s+/u', ' ', str_replace("\xc2\xa0", ' ', $s));

        return $normalize($expectedLine) === $normalize($actualLine);
    }
}
