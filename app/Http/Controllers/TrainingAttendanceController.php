<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LegacyTraining;
use App\Models\LegacyEmployee;
use App\Models\TrainingAttendance;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendanceNotification;
use App\Models\ProgramPermission;
use Illuminate\Support\Facades\Auth;

use App\Models\Evaluation;

class TrainingAttendanceController extends Controller
{
    public function index()
    {
        return view('training.attendance');
    }

    public function notifications()
    {
        // Get all records in 'To Evaluate' status
        // Usually, these are the ones where the 3-month period is running or finished
        $notifications = Evaluation::where('status', 'To Evaluate')
            ->orderBy('duedate', 'asc')
            ->get();

        return view('training.notifications', compact('notifications'));
    }

    public function searchTraining(Request $request)
    {
        $term = $request->query('term');
        $trainings = LegacyTraining::where('TRS', 'LIKE', "%$term%")
            ->orWhere('TName', 'LIKE', "%$term%")
            ->limit(10)
            ->get();

        return response()->json($trainings);
    }

    public function searchEmployee(Request $request)
    {
        $term = trim($request->query('term'));
        
        // Use local Employee model (MySQL) for much faster searching
        $employees = \App\Models\Employee::where('emp_no', 'LIKE', "$term%")
            ->orWhere('name', 'LIKE', "$term%")
            ->orWhere('name', 'LIKE', "% $term%")
            ->limit(15)
            ->get(['emp_no', 'name']);

        $results = $employees->map(function($emp) {
            return [
                'empno' => $emp->emp_no,
                'empname' => $emp->name
            ];
        });

        return response()->json($results);
    }

    public function getEmployeeDetails($empno)
    {
        $employee = LegacyEmployee::with('supervisor')->where('empno', $empno)->first();
        
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        return response()->json([
            'empno' => trim($employee->empno),
            'empname' => trim($employee->empname),
            'deptcode' => trim($employee->deptcode ?? ''),
            'seccode' => trim($employee->seccode ?? ''),
            'supervisor_no' => trim($employee->supercode ?? ''),
            'supervisor_name' => $employee->supervisor ? trim($employee->supervisor->empname) : '',
            'supervisor_email' => $employee->supervisor ? trim($employee->supervisor->email) : '',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_code' => 'required',
            'training_title' => 'required',
            'participants' => 'required|array|min:1',
            'participants.*.emp_no' => 'required',
        ]);

        $trainingData = $request->only([
            'training_code', 'training_title', 'start_date', 'end_date', 
            'time', 'venue', 'trainer_name', 'trainer_type'
        ]);

        foreach ($request->participants as $participant) {
            // Resolve Trainer Name if it looks like an EmpNo
            $trainerName = $trainingData['trainer_name'];
            if ($trainingData['trainer_type'] === 'Internal' && preg_match('/^[A-Z]?\d+$/i', $trainerName)) {
                $trainerEmp = LegacyEmployee::where('empno', trim($trainerName))->first();
                if ($trainerEmp) {
                    $trainerName = trim($trainerEmp->empname);
                }
            }

            // Generate RefNum for TE_0001
            $lastId = Evaluation::max('teid') ?? 0;
            $refnum = 'TEE' . date('y') . ($lastId + 1);

            $evaluation = Evaluation::create([
                'refnum' => $refnum,
                'empno' => $participant['emp_no'],
                'fullname' => $participant['emp_name'],
                'dept' => $participant['dept_code'],
                'sec' => $participant['sec_code'],
                'topic' => $trainingData['training_title'],
                'tcategory' => $trainingData['trainer_type'] ?: 'Internal',
                'entryin' => $trainingData['start_date'],
                'entryout' => $trainingData['end_date'],
                'status' => 'To Evaluate',
                'dtissued' => now()->format('Y-m-d'),
                'duedate' => \Carbon\Carbon::parse($trainingData['end_date'])->addMonths(3)->format('Y-m-d'),
                'eemp' => $participant['supervisor_no'],
                'ename' => $participant['supervisor_name'],
                'eemail' => $participant['supervisor_email'],
                'tduration' => '1', // Default duration
                'radiocom' => 'On the job observation', // Default methodology
                'remarkhr' => "Venue: {$trainingData['venue']}, Time: {$trainingData['time']}, Trainer: {$trainerName}"
            ]);

            // Send Email to Superior
            if ($evaluation->eemail) {
                try {
                    // We reuse the notification logic but passing the Evaluation model
                    Mail::to($evaluation->eemail)->send(new AttendanceNotification($evaluation));
                } catch (\Exception $e) {
                    \Log::error("Failed to send email to {$evaluation->eemail}: " . $e->getMessage());
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Attendance saved to TE_0001 and supervisors notified!']);
    }
}
