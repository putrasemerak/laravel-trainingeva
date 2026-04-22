<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\UserRole;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $testUsers = [
            [
                'emp_no' => 'DUMMY01',
                'name' => 'Admin Tester',
                'role' => 'admin',
                'email' => 'admin@test.local'
            ],
            [
                'emp_no' => 'DUMMY02',
                'name' => 'Evaluator Tester',
                'role' => 'evaluator',
                'email' => 'evaluator@test.local'
            ],
            [
                'emp_no' => 'DUMMY03',
                'name' => 'User Tester',
                'role' => 'user',
                'email' => 'user@test.local'
            ],
        ];

        foreach ($testUsers as $user) {
            Employee::updateOrCreate(
                ['emp_no' => $user['emp_no']],
                [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'division_code' => 'DIV01',
                    'department_code' => 'DEPT01',
                    'is_active' => true
                ]
            );

            UserRole::updateOrCreate(
                ['emp_no' => $user['emp_no']],
                ['role' => $user['role']]
            );
        }
    }
}
