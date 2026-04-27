<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evaluation;
use App\Models\LegacyEmployee;
use App\Models\LegacyTraining;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RealisticTrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Fetching realistic data from MSSQL legacy tables...');

        try {
            // 1. Get some real trainings from HR_0026
            $realTrainings = LegacyTraining::limit(10)->get();
            if ($realTrainings->isEmpty()) {
                $this->command->error('No training records found in HR_0026. Please ensure legacy connection is active.');
                return;
            }

            // 2. Get some real employees from SY_0100
            $realEmployees = LegacyEmployee::whereNotNull('email')
                ->where('email', '!=', '')
                ->limit(20)
                ->get();

            if ($realEmployees->isEmpty()) {
                $this->command->error('No employees with emails found in SY_0100.');
                return;
            }

            $this->command->info('Generating test records in TE_0001...');

            $bar = $this->command->getOutput()->createProgressBar(50);
            $bar->start();

            // Statuses to distribute
            $statuses = ['To Evaluate', 'Evaluated', 'Overdue', 'To Notify'];

            for ($i = 0; $i < 50; $i++) {
                $training = $realTrainings->random();
                $employee = $realEmployees->random();
                $status = $statuses[array_rand($statuses)];
                
                // Fetch supervisor info for realism
                $supervisor = null;
                if ($employee->supercode) {
                    $supervisor = LegacyEmployee::where('empno', $employee->supercode)->first();
                }

                // Use real training date if available, otherwise random
                $tDateRaw = $training->TSDate;
                if ($tDateRaw) {
                    $startDate = Carbon::parse($tDateRaw);
                } else {
                    $startDate = Carbon::now()->subMonths(rand(1, 6))->subDays(rand(1, 30));
                }
                
                $endDate = (clone $startDate)->addDays(rand(1, 3));
                $dueDate = (clone $endDate)->addMonths(3);

                // For Evaluated records, set random ratings
                $range = $status === 'Evaluated' ? rand(5, 10) : 0;
                $range2 = $status === 'Evaluated' ? rand(5, 10) : 0;
                $range3 = $status === 'Evaluated' ? rand(5, 10) : 0;
                $range4 = $status === 'Evaluated' ? rand(5, 10) : 0;
                $range5 = $status === 'Evaluated' ? rand(5, 10) : 0;
                $range6 = $status === 'Evaluated' ? rand(5, 10) : 0;
                
                $total = $status === 'Evaluated' ? ($range + $range2 + $range3 + $range4 + $range5 + $range6) / 6 : 0;

                // Generate RefNum
                $lastId = Evaluation::max('teid') ?? 0;
                $refnum = 'TEE26' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

                Evaluation::create([
                    'refnum' => $refnum,
                    'empno' => trim($employee->empno),
                    'fullname' => trim($employee->empname),
                    'dept' => trim($employee->deptcode ?? 'GEN'),
                    'sec' => trim($employee->seccode ?? 'GEN'),
                    'topic' => $training->TName,
                    'tcategory' => $training->TrainerType ?: 'Internal',
                    'entryin' => $startDate->format('Y-m-d'),
                    'entryout' => $endDate->format('Y-m-d'),
                    'status' => $status,
                    'dtissued' => $startDate->format('Y-m-d'),
                    'duedate' => $dueDate->format('Y-m-d'),
                    'eemp' => $supervisor ? trim($supervisor->empno) : 'SUPER01',
                    'ename' => $supervisor ? trim($supervisor->empname) : 'Default Supervisor',
                    'eemail' => $supervisor ? trim($supervisor->email) : 'supervisor@example.com',
                    'tduration' => rand(1, 4) . ' Days',
                    'radiocom' => 'On the job observation',
                    'remarkhr' => 'Automated test data for review',
                    'range' => $range,
                    'range2' => $range2,
                    'range3' => $range3,
                    'range4' => $range4,
                    'range5' => $range5,
                    'range6' => $range6,
                    'totaleffective' => round($total, 2),
                    'evaluator' => $status === 'Evaluated' ? 'Good performance shown after training.' : null,
                    'dtevaluate' => $status === 'Evaluated' ? Carbon::now()->format('Y-m-d') : null,
                ]);

                $bar->advance();
            }

            $bar->finish();
            $this->command->newLine();
            $this->command->info('Success! 50 realistic test records created in TE_0001.');

        } catch (\Exception $e) {
            $this->command->error('Error during seeding: ' . $e->getMessage());
        }
    }
}
