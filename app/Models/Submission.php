<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'timestamp',
        'nama',
        'departemen',
        'jabatan',
        'jenis_perangkat',
        'data',
        'synced_to_google',
    ];

    protected $casts = [
        'data' => 'array',
        'synced_to_google' => 'boolean',
    ];
}

