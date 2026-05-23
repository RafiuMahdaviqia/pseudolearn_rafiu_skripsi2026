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
        'difficulty',
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

    // Parse jawaban bank soal (JSON array atau teks per baris) menjadi daftar baris kode
    public static function parseJawabanLines(?string $raw): array
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return [];
        }

        if (str_starts_with($raw, '[')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $lines = [];
                foreach ($decoded as $line) {
                    if (!is_string($line)) {
                        continue;
                    }
                    $line = trim($line);
                    if ($line !== '') {
                        $lines[] = $line;
                    }
                }

                return array_values($lines);
            }
        }

        $lines = preg_split('/\R/', $raw) ?: [];

        return array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));
    }

    // Bandingkan dua baris kode Java, abaikan perbedaan spasi
    public static function linesMatch(string $kunci, string $jawaban): bool
    {
        $normalize = static fn (string $line) => preg_replace('/\s+/', '', trim($line)) ?? '';

        return $normalize($kunci) === $normalize($jawaban);
    }
}
