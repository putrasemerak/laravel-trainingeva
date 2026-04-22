<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['role', 'module', 'is_allowed'];

    protected $casts = [
        'is_allowed' => 'boolean',
    ];
}
