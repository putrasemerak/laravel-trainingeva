<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Models\ProgramPermission;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Map username to EmpNo for our model
        if (Auth::attempt(['EmpNo' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = $user->role; // This triggers the mapping relationship we added

            session(['user_role' => $role]);
            session(['user_name' => $user->EmpName]);

            // Log Audit Trail
            $description = "User logged in with role: $role";

            AuditTrail::create([
                'USER_ID' => $user->EmpNo,
                'USER_NAME' => $user->EmpName,
                'ACTION_TYPE' => 'LOGIN',
                'PAGE_NAME' => 'Login Page',
                'DESCRIPTION' => $description,
                'IP_ADDRESS' => $request->ip(),
                'ADDDATE' => Carbon::now()->format('Y-m-d'),
                'ADDTIME' => Carbon::now()->format('H:i:s'),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
