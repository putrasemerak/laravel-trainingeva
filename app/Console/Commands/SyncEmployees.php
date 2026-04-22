<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Employee; // We'll update this model next

class SyncEmployees extends Command
{
    protected $signature = 'sync:employees';
    protected $description = 'Sync employee data from legacy MS SQL to local MySQL';

    public function handle()
    {
        $this->info('Starting employee sync...');

        // Fetch from legacy
        $legacyEmployees = DB::connection('sqlsrv')->table('SY_0100')->get();
        $bar = $this->output->createProgressBar(count($legacyEmployees));

        foreach ($legacyEmployees as $legacy) {
            DB::table('employees')->updateOrInsert(
                ['emp_no' => trim($legacy->empno)],
                [
                    'name' => trim($legacy->empname),
                    'division_code' => trim($legacy->DivCode ?? ''),
                    'department_code' => trim($legacy->deptcode ?? ''),
                    'section_code' => trim($legacy->seccode ?? ''),
                    'subsection_code' => trim($legacy->subseccode ?? ''),
                    'unit_code' => trim($legacy->UnitCode ?? ''),
                    'supervisor_no' => trim($legacy->supercode ?? ''),
                    'email' => trim($legacy->email ?? ''),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sync completed successfully!');
    }
}
