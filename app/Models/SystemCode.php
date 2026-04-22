<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemCode extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'SY_0180';
    protected $primaryKey = 'CODE';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'CODE', 'CODENAME'
    ];
}
