<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Employee;
use App\Models\AuditTrail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    /**
     * Show registration form
     */
    public function create()
    {
        return view('admin.user_register');
    }

    /**
     * Lookup user from SY_0100 (Mirrored in Local MySQL)
     */
    public function lookup($empno)
    {
        try {
            // Search in mirrored SY_0100 table in MySQL connection
            $legacyUser = DB::connection('mysql')
                ->table('sy_0100') // MySQL table names are usually lowercase, adjust if needed
                ->where('EmpNo', $empno)
                ->first();

            if (!$legacyUser) {
                // Try uppercase just in case
                $legacyUser = DB::connection('mysql')
                    ->table('SY_0100')
                    ->where('EmpNo', $empno)
                    ->first();
            }

            if (!$legacyUser) {
                return response()->json(['error' => 'Employee not found in mirrored SY_0100 system.'], 404);
            }

            return response()->json([
                'empno' => trim($legacyUser->EmpNo),
                'name' => trim($legacyUser->EmpName),
                'dept' => trim($legacyUser->Dept ?? ''),
                'div' => trim($legacyUser->Div ?? ''),
                'position' => trim($legacyUser->Post ?? ''),
                'email' => trim($legacyUser->Email ?? ''),
            ]);
        } catch (\Exception $e) {
            \Log::error("Legacy lookup failed: " . $e->getMessage());
            return response()->json(['error' => 'Table SY_0100 not found in local database or query failed.'], 500);
        }
    }

    /**
     * Save to local tables
     */
    public function store(Request $request)
    {
        $request->validate([
            'emp_no' => 'required|string',
            'name' => 'required|string',
            'role' => 'required|in:superuser,admin,evaluator,user',
        ]);

        try {
            // Check if already registered
            $exists = UserRole::where('emp_no', $request->emp_no)->first();
            
            if ($exists && !$request->has('force_update')) {
                return redirect()->back()->with('user_exists_data', $request->all())->withInput();
            }

            // 1. Create/Update Local Employee Record
            Employee::updateOrCreate(
                ['emp_no' => $request->emp_no],
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'division_code' => $request->div ?? 'LOCAL',
                    'department_code' => $request->dept ?? 'LOCAL',
                    'is_active' => true
                ]
            );

            // 2. Assign Role (Update if force_update is present)
            UserRole::updateOrCreate(
                ['emp_no' => $request->emp_no],
                ['role' => $request->role]
            );

            $actionText = $request->has('force_update') ? "Updated" : "Registered";
            $this->logAudit('CREATE', 'User Registration', "$actionText local user {$request->emp_no} with role {$request->role}");

            return redirect()->back()->with('success_card', "Successfully $actionText: {$request->name} ({$request->emp_no}) has been granted {$request->role} access.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error_card', "Operation failed: " . $e->getMessage())->withInput();
        }
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
