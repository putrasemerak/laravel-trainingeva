<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\SystemCode;

class EmployeeController extends Controller
{
    public function show($empno)
    {
        // Use our User model's find logic which already handles SQLSRV -> Local fallback
        $user = \App\Models\User::find($empno);

        if (!$user) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Try to get detailed info from the Employee model (which might be local or linked via view)
        $employee = Employee::where('emp_no', $empno)->first();

        $data = [
            'empno' => $user->EmpNo,
            'name' => $user->EmpName,
            'empname' => $user->EmpName,
        ];

        if ($employee) {
            $data = array_merge($data, $employee->toArray());
            $data['division_name'] = $this->getCodeName($employee->division_code);
            $data['department_name'] = $this->getCodeName($employee->department_code);
            $data['section_name'] = $this->getCodeName($employee->section_code);
            $data['subsection_name'] = $this->getCodeName($employee->subsection_code);
            $data['unit_name'] = $this->getCodeName($employee->unit_code);

            // Get Supervisor details from local table
            $supervisor = Employee::where('emp_no', $employee->supervisor_no)->first();
            if ($supervisor) {
                $data['evaluator_name'] = $supervisor->name;
                $data['evaluator_email'] = $supervisor->email;
                $data['supercode'] = $supervisor->emp_no;
            }
        }

        // Get Training History from HR_0020 (Still from sqlsrv)
        try {
            $data['training_history'] = \App\Models\TrainingRecord::where('EmpNo', $empno)
                ->orderBy('TDate', 'DESC')
                ->get();
        } catch (\Exception $e) {
            $data['training_history'] = [];
        }

        return response()->json($data);
    }

    public function getTopics()
    {
        $topics = \App\Models\TrainingTopic::select('TopicNo', 'Topic')
            ->orderBy('Topic', 'ASC')
            ->get();
        return response()->json($topics);
    }

    public function getTrainingDetail(Request $request)
    {
        $empNo = $request->query('empno');
        $title = $request->query('title');
        $tdate = $request->query('tdate');

        if (!$empNo || !$title || !$tdate) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        // Try to find in Evaluation table first (TE_0001)
        $eval = \App\Models\Evaluation::where('empno', $empNo)
            ->where('topic', $title)
            ->where('entryin', $tdate)
            ->where('status', 'Evaluated')
            ->first();

        // Also get the base record from HR_0020
        $record = \App\Models\TrainingRecord::where('EmpNo', $empNo)
            ->where('Title', $title)
            ->where('TDate', $tdate)
            ->first();

        if (!$record && !$eval) {
            return response()->json(['error' => 'Training record not found'], 404);
        }

        // Merge data if evaluation exists
        $data = $record ? $record->toArray() : [];
        if ($eval) {
            $data['eval_data'] = $eval->toArray();
            // Ensure topic and date are present if HR record was missing but eval existed
            $data['Title'] = $eval->topic;
            $data['TDate'] = $eval->entryin;
            $data['Status'] = $eval->status;
        }

        return response()->json($data);
    }

    private function getCodeName($code)
    {
        if (!$code) return '-';
        $systemCode = SystemCode::where('CODE', $code)->first();
        return $systemCode ? $systemCode->CODENAME : '-';
    }
}
