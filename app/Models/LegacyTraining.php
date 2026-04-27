<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyTraining extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'HR_0026';
    protected $primaryKey = 'TRS';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'TRS', 'TName', 'TSDate', 'TEDate', 'TSTime', 'TETime', 'Venue', 'Trainer', 'TrainerType', 'Status'
    ];
}
