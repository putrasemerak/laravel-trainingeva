<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\SystemCode;

class EmployeeController extends Controller
{
    public function show($empno)
    {
        $employee = Employee::where('emp_no', $empno)->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Get CODENAMEs for various codes
        $data = $employee->toArray();
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

        // Get Training History from HR_0020 (Still from sqlsrv)
        $data['training_history'] = \App\Models\TrainingRecord::where('EmpNo', $empno)
            ->orderBy('TDate', 'DESC')
            ->get();

        return response()->json($data);
    }

    public function getTopics()
    {
        $topics = \App\Models\TrainingTopic::select('TopicNo', 'Topic')
            ->orderBy('Topic', 'ASC')
            ->get();
        return response()->json($topics);
    }

    private function getCodeName($code)
    {
        if (!$code) return '-';
        $systemCode = SystemCode::where('CODE', $code)->first();
        return $systemCode ? $systemCode->CODENAME : '-';
    }
}
