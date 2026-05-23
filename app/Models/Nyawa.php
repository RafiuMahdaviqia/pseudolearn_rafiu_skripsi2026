<?php

namespace App\Models;

use Carbon\Carbon;
use App\Core\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Nyawa extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'nyawa';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'id_user',
        'id_mahasiswa',
        'nyawa',
        'max_nyawa',
        'next_regen_at',
    ];

    protected $casts = [
        'nyawa' => 'integer',
        'max_nyawa' => 'integer',
        'next_regen_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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

    /**
     * Check and regenerate nyawa based on time.
     * Regenerates 10 nyawa every minute when below max (max 100).
     */
    public function checkAndRegenerate(): self
    {
        $now = Carbon::now();

        // If already at max, ensure timer is cleared
        if ($this->nyawa >= $this->max_nyawa) {
            if ($this->next_regen_at !== null) {
                $this->next_regen_at = null;
                $this->save();
            }
            return $this;
        }

        // Below max - handle regeneration
        if ($this->next_regen_at === null) {
            // Start regeneration timer (first time below max)
            $this->next_regen_at = $now->copy()->addMinute();
            $this->save();
            return $this;
        }

        $nextRegen = Carbon::parse($this->next_regen_at);

        // Check if regeneration time has passed
        if ($now->gte($nextRegen)) {
            // Calculate total minutes since regen timer started
            $minutesSinceRegen = $nextRegen->diffInMinutes($now);
            // Each 1 minute = 10 lives, plus 10 for crossing the initial threshold
            $livesToAdd = ((int) floor($minutesSinceRegen / 1) + 1) * 10;

            $this->nyawa = min($this->nyawa + $livesToAdd, $this->max_nyawa);

            if ($this->nyawa < $this->max_nyawa) {
                // Set next regen relative to now
                $this->next_regen_at = $now->copy()->addMinute();
            } else {
                $this->next_regen_at = null;
            }

            $this->save();
        }

        return $this;
    }
}
