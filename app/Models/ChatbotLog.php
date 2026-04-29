<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLog extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'chatbot_logs';

    protected $fillable = [
        'id_mahasiswa',
        'access_id',
        'id_level',
        'id_soal',
        'type',
        'pesan',
        'respons',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id');
    }

    public function accessLog(): BelongsTo
    {
        return $this->belongsTo(ChatbotAccessLog::class, 'access_id', 'id');
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