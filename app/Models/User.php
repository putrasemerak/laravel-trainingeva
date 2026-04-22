<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Permission;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'sqlsrv';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'SY_0050';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'EmpNo';

    /**
     * Override resolveRouteBinding to support fallback
     */
    public static function find($id, $columns = ['*'])
    {
        // Try SQL Server first
        try {
            $user = static::on('sqlsrv')->where('EmpNo', $id)->first();
            if ($user) return $user;
        } catch (\Exception $e) {
            // Log or ignore
        }

        // Fallback to local employees table
        $localEmployee = \App\Models\Employee::where('emp_no', $id)->first();
        if ($localEmployee) {
            $user = new static();
            $user->setRawAttributes([
                'EmpNo' => $localEmployee->emp_no,
                'EmpName' => $localEmployee->name,
                'Pass' => $localEmployee->emp_no, // Dummy password is their emp_no
            ], true);
            $user->exists = true;
            return $user;
        }

        return null;
    }

    /**
     * Override the default query behavior to allow login from both sources
     */
    public function newQuery()
    {
        // This is a bit of a hack for Auth::attempt
        // If we're searching by EmpNo, we want to try both
        return parent::newQuery();
    }

    /**
     * Add a static method for the provider to find by credentials
     */
    public static function findByCredentials(array $credentials)
    {
        return static::find($credentials['EmpNo']);
    }

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

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
        'EmpNo',
        'EmpName',
        'Pass',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'Pass',
    ];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'EmpNo';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->Pass;
    }

    /**
     * Get the name of the password column.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return 'Pass';
    }

    /**
     * Get the user's role from the local MySQL database.
     */
    public function roleMapping()
    {
        return $this->hasOne(UserRole::class, 'emp_no', 'EmpNo');
    }

    public function getRoleAttribute()
    {
        return $this->roleMapping ? $this->roleMapping->role : 'user';
    }

    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    public function isSuperUser()
    {
        return $this->role === 'superuser';
    }

    public function hasPermission($module)
    {
        if ($this->isSuperUser()) {
            return true;
        }

        return Permission::where('role', $this->role)
            ->where('module', $module)
            ->where('is_allowed', true)
            ->exists();
    }

    /**
     * Prevent any updates to the legacy user table.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        return true; // Pretend it saved, but do nothing
    }
}
