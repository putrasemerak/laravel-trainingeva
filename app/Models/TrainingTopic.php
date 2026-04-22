<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingTopic extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'IN_0150';
    protected $primaryKey = 'TopicNo';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}
