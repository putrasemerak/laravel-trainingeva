<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TE_AUDIT_TRAIL';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ID'; // Assuming there is an ID, if not I will adjust.

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'USER_ID',
        'USER_NAME',
        'ACTION_TYPE',
        'PAGE_NAME',
        'DESCRIPTION',
        'IP_ADDRESS',
        'ADDDATE',
        'ADDTIME',
    ];
}
