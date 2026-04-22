<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramPermission extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'SY_0055N';
    public $timestamps = false;

    protected $fillable = [
        'ProgID', 'EmpNo', 'ALevel', 'Status'
    ];
}
