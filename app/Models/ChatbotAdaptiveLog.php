<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotAdaptiveLog extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'chatbot_adaptive_logs';

    protected $fillable = [
        'id_mahasiswa',
        'id_level',
        'id_soal',
        'nim',
        'nama',
        'kelas',
        'level_soal',
        'jenis_soal',
        'labeling',
        'pesan_bimbingan',
        'jumlah_langkah',
        'waktu_mulai',
        'waktu_selesai',
        'durasi_menit',
        'detail',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'detail' => 'array',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'id_level', 'id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'id_soal', 'id');
    }
}
