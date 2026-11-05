<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MikrotikAccount extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama_pemilik',
        'tim',
        'jabatan',
        'username',
        'password',
        'profile_hotspot',
        'rate_limit',
        'command_terminal',
        'status',
        'catatan',
        'created_at_str',
    ];
}

