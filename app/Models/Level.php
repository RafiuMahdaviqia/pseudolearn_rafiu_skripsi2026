<?php

namespace App\Models;

use App\Core\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Level extends BaseModel
{
   use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'level';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'image',
        'feedback_data_type',
        'feedback_algorithm',
        'order',
        'manual_active' // 0: nonaktif, 1: aktif
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
}
