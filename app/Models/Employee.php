<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    // Local MySQL connection
    protected $connection = 'mysql';
    protected $table = 'employees';

    protected $fillable = [
        'emp_no', 'name', 'division_code', 'department_code', 'section_code', 
        'subsection_code', 'unit_code', 'supervisor_no', 'email', 'is_active'
    ];

    /**
     * Get supervisor name from local table
     */
    public function supervisor()
    {
        return $this->hasOne(Employee::class, 'emp_no', 'supervisor_no');
    }
}
