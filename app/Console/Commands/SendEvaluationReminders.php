<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evaluation;
use App\Mail\EvaluationReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendEvaluationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluations:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically send evaluation requests to supervisors when the 3-month period is due';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        $this->info("Checking for evaluations due on: {$today}");

        // Find evaluations where duedate is today and still pending
        // We also pick up records from the last 3 days just in case the scheduler missed a run
        $pendingEvaluations = Evaluation::where('status', 'To Evaluate')
            ->whereBetween('duedate', [Carbon::now()->subDays(3)->format('Y-m-d'), $today])
            ->get();

        if ($pendingEvaluations->isEmpty()) {
            $this->info('No evaluations due for reminder today.');
            return;
        }

        $bar = $this->output->createProgressBar($pendingEvaluations->count());

        foreach ($pendingEvaluations as $evaluation) {
            if ($evaluation->eemail) {
                try {
                    Mail::to($evaluation->eemail)->send(new EvaluationReminder($evaluation));
                    $this->line("\nSent reminder for {$evaluation->refnum} to {$evaluation->eemail}");
                } catch (\Exception $e) {
                    $this->error("\nFailed to send reminder for {$evaluation->refnum}: " . $e->getMessage());
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Evaluation reminders blast completed.');
    }
}
