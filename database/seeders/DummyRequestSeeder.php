<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evaluation;
use Carbon\Carbon;

class DummyRequestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Evaluated Request
        Evaluation::create([
            'refnum' => 'TEE26001',
            'empno' => 'DUMMY03',
            'fullname' => 'User Tester',
            'div' => 'DIV01',
            'dept' => 'DEPT01',
            'topic' => 'HEALTH AND SAFETY AT WORK',
            'entryin' => '2025-10-15',
            'entryout' => '2025-10-16',
            'tduration' => '2 Days',
            'eemp' => 'DUMMY02',
            'ename' => 'Evaluator Tester',
            'eemail' => 'evaluator@test.local',
            'status' => 'Evaluated',
            'dtissued' => '2025-10-17',
            'duedate' => '2026-04-16',
            'dtevaluate' => '2025-11-20',
            'totaleffective' => 8.5,
            'range' => 8, 'range2' => 9, 'range3' => 8, 'range4' => 9, 'range5' => 8, 'range6' => 9,
        ]);

        // 2. Pending Request
        Evaluation::create([
            'refnum' => 'TEE26002',
            'empno' => 'DUMMY03',
            'fullname' => 'User Tester',
            'div' => 'DIV01',
            'dept' => 'DEPT01',
            'topic' => 'EXCEL ADVANCED TRAINING',
            'entryin' => '2026-03-01',
            'entryout' => '2026-03-02',
            'tduration' => '2 Days',
            'eemp' => 'DUMMY02',
            'ename' => 'Evaluator Tester',
            'eemail' => 'evaluator@test.local',
            'status' => 'To Evaluate',
            'dtissued' => '2026-03-05',
            'duedate' => '2026-09-02',
        ]);

        // 3. Overdue Request
        Evaluation::create([
            'refnum' => 'TEE26003',
            'empno' => 'DUMMY03',
            'fullname' => 'User Tester',
            'div' => 'DIV01',
            'dept' => 'DEPT01',
            'topic' => 'ISO 9001 AWARENESS',
            'entryin' => '2025-05-01',
            'entryout' => '2025-05-02',
            'tduration' => '2 Days',
            'eemp' => 'DUMMY02',
            'ename' => 'Evaluator Tester',
            'eemail' => 'evaluator@test.local',
            'status' => 'Overdue',
            'dtissued' => '2025-05-10',
            'duedate' => '2025-11-02',
        ]);
    }
}
