<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatbotAdaptiveLog extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'chatbot_adaptive_logs';

    protected $fillable = [
        'id_mahasiswa',
        'nim',
        'nama',
        'id_kelas',
        'kelas',
        'id_level',
        'level_soal',
        'id_soal',
        'jenis_soal',
        'jumlah_langkah',
        'waktu_mulai',
        'waktu_selesai',
        'id_label_skor',
        'labeling',
        'durasi_menit',
        'total_akses_chatbot_adaptive',
        'pesan_bimbingan',
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
