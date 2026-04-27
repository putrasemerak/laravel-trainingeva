<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyEmployee extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'SY_0100';
    protected $primaryKey = 'empno';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function supervisor()
    {
        return $this->hasOne(LegacyEmployee::class, 'empno', 'supercode');
    }
}
