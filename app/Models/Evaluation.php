<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $connection = 'mysql';
    protected $table = 'te_0001';
    protected $primaryKey = 'teid';
    public $timestamps = false;

    protected $fillable = [
        'refnum', 'fullname', 'empno', 'div', 'dept', 'sec', 'subsec', 'unit',
        'tcategory', 'topic', 'entryin', 'entryout', 'tduration', 'radiocom',
        'tresult', 'range', 'range2', 'range3', 'range4', 'range5', 'range6',
        'evaluator', 'remarkhr', 'totaleffective', 'status', 'dtissued',
        'duedate', 'ename', 'eemp', 'eemail', 'dtevaluate'
    ];
}
