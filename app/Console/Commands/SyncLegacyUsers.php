<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SyncLegacyUsers extends Command
{
    protected $signature = 'sync:legacy-users';
    protected $description = 'Sync SY_0100 from SQL Server to Local MySQL';

    public function handle()
    {
        $this->info('Starting sync from SQL Server SY_0100...');

        try {
            // 1. Fetch data from Legacy
            $legacyData = DB::connection('sqlsrv')->table('SY_0100')->get();
            
            if ($legacyData->isEmpty()) {
                $this->error('No data found in legacy SY_0100 table.');
                return;
            }

            // 2. Ensure Local Table exists
            if (!Schema::connection('mysql')->hasTable('sy_0100')) {
                $this->info('Creating local sy_0100 table...');
                Schema::connection('mysql')->create('sy_0100', function (Blueprint $table) {
                    $table->string('EmpNo')->primary();
                    $table->string('EmpName')->nullable();
                    $table->string('Dept')->nullable();
                    $table->string('Div')->nullable();
                    $table->string('Post')->nullable();
                    $table->string('Email')->nullable();
                    $table->timestamps();
                });
            }

            // 3. Sync Data
            $this->info('Transferring ' . $legacyData->count() . ' records...');
            
            DB::connection('mysql')->table('sy_0100')->truncate();

            foreach ($legacyData as $row) {
                // Convert to array to handle lowercase keys correctly
                $data = (array)$row;
                
                DB::connection('mysql')->table('sy_0100')->updateOrInsert(
                    ['EmpNo' => trim($data['empno'])],
                    [
                        'EmpName' => trim($data['empname']),
                        'Dept' => trim($data['deptcode'] ?? ''),
                        'Div' => trim($data['DivCode'] ?? ''),
                        'Post' => trim($data['Designation'] ?? $data['postcode'] ?? ''),
                        'Email' => trim($data['email'] ?? ''),
                        'updated_at' => now(),
                    ]
                );
            }

            $this->info('Sync completed successfully!');
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
        }
    }
}
