<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingRecord;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Training History from HR_0020
        $history = TrainingRecord::where('EmpNo', $user->EmpNo)
            ->orderBy('TDate', 'DESC')
            ->get();

        // 2. My Evaluation Requests (Self or HR created where I am the subject)
        $myEvaluations = Evaluation::where('empno', $user->EmpNo)
            ->orderBy('dtissued', 'DESC')
            ->get();

        // 3. Pending Evaluations (Where I am the Evaluator)
        $pendingTasks = Evaluation::where('eemp', $user->EmpNo)
            ->where('status', '!=', 'Evaluated')
            ->orderBy('duedate', 'ASC')
            ->get();

        return view('user.dashboard', compact('history', 'myEvaluations', 'pendingTasks'));
    }
}
