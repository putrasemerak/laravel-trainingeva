<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTableSchema extends Command
{
    protected $signature = 'db:schema {table} {connection=sqlsrv}';
    protected $description = 'Check columns of a table';

    public function handle()
    {
        $table = $this->argument('table');
        $connection = $this->argument('connection');

        $this->info("Checking columns for table: $table on connection: $connection");

        try {
            $columns = DB::connection($connection)->select("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?", [$table]);

            if (empty($columns)) {
                $this->error("No columns found for table: $table");
                return;
            }

            foreach ($columns as $column) {
                $this->line("- {$column->COLUMN_NAME} ({$column->DATA_TYPE})");
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
