<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotAccessLog extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'chatbot_access_logs';

    protected $fillable = [
        'id_mahasiswa',
        'type',
        'opened_at',
        'closed_at',
        'durasi_menit',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id');
    }
}
