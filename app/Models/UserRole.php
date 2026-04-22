<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    // Explicitly set to mysql to prevent cross-database confusion
    protected $connection = 'mysql';
    protected $table = 'user_roles';
    
    protected $fillable = [
        'emp_no',
        'role'
    ];
}
