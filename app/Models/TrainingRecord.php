<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingRecord extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'HR_0020';
    public $timestamps = false;

    // This table doesn't have a standard single primary key incrementing
    public $incrementing = false;

    protected $fillable = [
        'EmpNo', 'Title', 'TDate', 'TopicNo', 'NOD', 'Cost', 'Venue', 'Trainer', 'Period', 'Category', 'Status'
    ];
}
