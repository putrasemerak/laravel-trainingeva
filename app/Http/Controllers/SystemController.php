<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRole;
use App\Models\SystemSetting;
use App\Models\AuditTrail;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    public function index()
    {
        $roles = UserRole::all();
        $settings = SystemSetting::all();
        $permissions = Permission::all()->groupBy('role');
        
        // Define available modules
        $modules = ['dashboard', 'evaluation_list', 'evaluation_create', 'audit_trail', 'system_settings', 'evaluation_request'];
        
        // Ensure all roles have all modules in the permissions collection for the view
        $allRoles = ['superuser', 'admin', 'evaluator', 'user'];
        
        return view('admin.settings', compact('roles', 'settings', 'permissions', 'modules', 'allRoles'));
    }

    public function updatePermission(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
            'module' => 'required|string',
            'is_allowed' => 'required|boolean'
        ]);

        Permission::updateOrCreate(
            ['role' => $request->role, 'module' => $request->module],
            ['is_allowed' => $request->is_allowed]
        );

        $status = $request->is_allowed ? 'ALLOWED' : 'DENIED';
        $this->logAudit('UPDATE', 'System Settings', "Updated permission for {$request->role} on module {$request->module} to {$status}");

        return response()->json(['success' => true]);
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'emp_no' => 'required|string',
            'role' => 'required|in:superuser,admin,evaluator,user',
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'register_local' => 'nullable|boolean'
        ]);

        // If explicitly requested to register locally OR if details are provided for a new local user
        if ($request->register_local || ($request->name && $request->email)) {
            \App\Models\Employee::updateOrCreate(
                ['emp_no' => $request->emp_no],
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'is_active' => true,
                    // Default values for local creation
                    'division_code' => 'LOCAL',
                    'department_code' => 'LOCAL',
                ]
            );
            $msg = "Registered local user and assigned role.";
        } else {
            $msg = "User role updated successfully.";
        }

        UserRole::updateOrCreate(
            ['emp_no' => $request->emp_no],
            ['role' => $request->role]
        );

        $this->logAudit('UPDATE', 'System Settings', "Updated role for {$request->emp_no} to {$request->role}");

        return redirect()->back()->with('success', $msg);
    }

    public function updateSetting(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'nullable|string'
        ]);

        SystemSetting::updateOrCreate(
            ['key' => $request->key],
            ['value' => $request->value]
        );

        $this->logAudit('UPDATE', 'System Settings', "Updated setting {$request->key}");

        return redirect()->back()->with('success', 'Setting updated successfully.');
    }

    private function logAudit($action, $page, $description)
    {
        $user = Auth::user();
        AuditTrail::create([
            'USER_ID' => $user->EmpNo,
            'USER_NAME' => $user->EmpName,
            'ACTION_TYPE' => $action,
            'PAGE_NAME' => $page,
            'DESCRIPTION' => $description,
            'IP_ADDRESS' => request()->ip(),
            'ADDDATE' => Carbon::now()->format('Y-m-d'),
            'ADDTIME' => Carbon::now()->format('H:i:s'),
        ]);
    }
}
