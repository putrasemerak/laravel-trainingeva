<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\AuditTrail;
use App\Mail\EvaluationNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        // Auto-update Overdue status
        Evaluation::where('status', '!=', 'Evaluated')
            ->where('duedate', '<', Carbon::now()->format('Y-m-d'))
            ->update(['status' => 'Overdue']);

        $evaluations = Evaluation::all();
        
        $this->logAudit('VIEW', 'Evaluation List Page (Admin)', 'User accessed evaluation list page');

        return view('evaluations.index', compact('evaluations'));
    }

    public function evaluatorIndex(Request $request)
    {
        $user = Auth::user();
        
        // Auto-update Overdue status for this evaluator's records
        Evaluation::where('eemp', $user->EmpNo)
            ->where('status', '!=', 'Evaluated')
            ->where('duedate', '<', Carbon::now()->format('Y-m-d'))
            ->update(['status' => 'Overdue']);

        $evaluations = Evaluation::where('eemp', $user->EmpNo)->get();

        $this->logAudit('VIEW', 'Evaluation List Page (Evaluator)', 'User accessed evaluation list page (Evaluator)');

        return view('evaluations.evaluator_index', compact('evaluations'));
    }

    public function createByUser()
    {
        return view('user.create_evaluation');
    }

    public function storeFromUser(Request $request)
    {
        $data = $request->validate([
            'empno' => 'required|string',
            'fullname' => 'required|string',
            'div' => 'required|string',
            'dept' => 'required|string',
            'sec' => 'nullable|string',
            'subsec' => 'nullable|string',
            'unit' => 'nullable|string',
            'topic' => 'required|string',
            'entryin' => 'required|date',
            'entryout' => 'required|date',
            'tduration' => 'required|string',
            'eemp' => 'required|string',
            'ename' => 'required|string',
            'eemail' => 'required|email',
        ]);

        // Generate RefNum
        $lastId = Evaluation::max('teid') ?? 0;
        $refnum = 'TEE' . date('y') . ($lastId + 1);

        $evaluation = new Evaluation();
        $evaluation->refnum = $refnum;
        $evaluation->empno = $data['empno'];
        $evaluation->fullname = $data['fullname'];
        $evaluation->div = $data['div'];
        $evaluation->dept = $data['dept'];
        $evaluation->sec = $data['sec'] ?? '';
        $evaluation->subsec = $data['subsec'] ?? '';
        $evaluation->unit = $data['unit'] ?? '';
        $evaluation->topic = $data['topic'];
        $evaluation->entryin = $data['entryin'];
        $evaluation->entryout = $data['entryout'];
        $evaluation->tduration = $data['tduration'];
        $evaluation->eemp = $data['eemp'];
        $evaluation->ename = $data['ename'];
        $evaluation->eemail = $data['eemail'];
        
        $evaluation->status = 'To Evaluate';
        $evaluation->dtissued = Carbon::now()->format('Y-m-d');
        $evaluation->duedate = Carbon::parse($data['entryout'])->addMonths(6)->format('Y-m-d');
        $evaluation->tcategory = 'Internal'; 
        $evaluation->radiocom = 'On the job observation';
        $evaluation->save();

        if ($evaluation->status === 'To Evaluate') {
            $this->sendEvaluatorEmail($evaluation);
        }

        $this->logAudit('INSERT', 'User Self-Evaluation', "User {$data['empno']} submitted evaluation request: $refnum");

        return redirect()->back()->with('success', "Evaluation $refnum submitted successfully to {$data['ename']}.");
    }

    public function create()
    {
        $this->logAudit('VIEW', 'Create Evaluation Page', 'User accessed create evaluation page');
        return view('evaluations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dtissued' => 'required|date',
            'empno' => 'required|string',
            'fullname' => 'required|string',
            'div' => 'required|string',
            'dept' => 'required|string',
            'sec' => 'required|string',
            'subsec' => 'required|string',
            'unit' => 'required|string',
            'radiocom2' => 'required|string', // Category: External/Internal
            'topic' => 'nullable|string',
            'topic2' => 'nullable|string',
            'entryin' => 'required|date',
            'entryout' => 'required|date',
            'tduration' => 'required|string',
            'tresult' => 'required|string',
            'radiocom' => 'required|string', // Methodology
            'status' => 'required|string',
            'duedate' => 'required|date',
            'eemp' => 'required|string',
            'ename' => 'required|string',
            'eemail' => 'required|email',
            'remarkhr' => 'nullable|string',
        ]);

        // Topic logic
        $topic = $data['topic'] ?: $data['topic2'];
        
        // Generate RefNum
        $lastId = Evaluation::max('teid');
        $refnum = 'TEE' . date('y') . ($lastId + 1);

        $evaluation = new Evaluation($data);
        $evaluation->refnum = $refnum;
        $evaluation->topic = $topic;
        $evaluation->tcategory = $data['radiocom2'];
        $evaluation->save();

        if ($evaluation->status === 'To Evaluate') {
            $this->sendEvaluatorEmail($evaluation);
        }

        $this->logAudit('INSERT', 'Create Evaluation', "Created evaluation record: $refnum for employee: {$data['empno']}");

        return redirect()->route('dashboard')->with('success', "Evaluation $refnum created successfully.");
    }

    public function evaluate($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        
        $this->logAudit('VIEW', 'Evaluation Form', "User accessed evaluation form for record: {$evaluation->refnum}");

        return view('evaluations.evaluate', compact('evaluation'));
    }

    public function show($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        
        $this->logAudit('VIEW', 'Evaluation View', "User viewed record: {$evaluation->refnum}");

        return view('evaluations.show', compact('evaluation'));
    }

    public function print($id)
    {
        $evaluation = Evaluation::findOrFail($id);
        
        $this->logAudit('PRINT', 'Evaluation Print', "User printed record: {$evaluation->refnum}");

        return view('evaluations.print', compact('evaluation'));
    }

    public function export(Request $request)
    {
        $selectedIds = $request->selected_records;
        if (!$selectedIds) {
            return back()->with('error', 'No records selected for export.');
        }

        $records = Evaluation::whereIn('teid', $selectedIds)->orderBy('entryin', 'DESC')->get();

        $filename = "training_effectiveness_export_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Ref No', 'Employee No', 'Name', 'Div', 'Dept', 'Sec', 'Subsec', 'Unit', 
            'Category', 'Topic', 'Start Date', 'End Date', 'Duration', 'Result', 
            'Status', 'Due Date', 'Evaluator', 'Evaluator Email', 'Remark TND', 
            'Knowledge Rating', 'Skill Rating', 'Delivery Rating', 'Initiative Rating', 
            'Error Reduction Rating', 'Work Quality Rating', 'Total Rating', 
            'Effectiveness', 'Re-Training Required', 'Evaluator Comment', 'Evaluation Date'
        ];

        $callback = function() use($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $row) {
                $effectiveness = 'N/A';
                $retraining = 'No';
                
                if (!is_null($row->totaleffective)) {
                    $effectiveness = $row->totaleffective >= 5 ? 'Effective' : 'Not Effective';
                    $retraining = $row->totaleffective >= 5 ? 'No' : 'Yes';
                }

                fputcsv($file, [
                    $row->refnum, $row->empno, $row->fullname, $row->div, $row->dept, $row->sec, $row->subsec, $row->unit,
                    $row->tcategory, $row->topic, $row->entryin, $row->entryout, $row->tduration, $row->tresult,
                    $row->status, $row->duedate, $row->ename . ' (' . $row->eemp . ')', $row->eemail, $row->remarkhr,
                    $row->range, $row->range2, $row->range3, $row->range4, $row->range5, $row->range6,
                    $row->totaleffective, $effectiveness, $retraining, $row->evaluator, $row->dtevaluate
                ]);
            }
            fclose($file);
        };

        $this->logAudit('EXPORT', 'Evaluation List', "User exported " . count($selectedIds) . " records to CSV");

        return response()->stream($callback, 200, $headers);
    }

    public function update(Request $request, $id)
    {
        $evaluation = Evaluation::findOrFail($id);
        $oldStatus = $evaluation->status;
        $oldData = $evaluation->toArray();

        $data = $request->validate([
            'range' => 'required|integer|min:0|max:10',
            'range2' => 'required|integer|min:0|max:10',
            'range3' => 'required|integer|min:0|max:10',
            'range4' => 'required|integer|min:0|max:10',
            'range5' => 'required|integer|min:0|max:10',
            'range6' => 'required|integer|min:0|max:10',
            'evaluator' => 'nullable|string',
            'totaleffective' => 'required|numeric',
            'status' => 'nullable|string' // Allow status update if admin is editing
        ]);

        $evaluation->update([
            'range' => $data['range'],
            'range2' => $data['range2'],
            'range3' => $data['range3'],
            'range4' => $data['range4'],
            'range5' => $data['range5'],
            'range6' => $data['range6'],
            'evaluator' => $data['evaluator'],
            'totaleffective' => $data['totaleffective'],
            'status' => $data['status'] ?? 'Evaluated',
            'dtevaluate' => Carbon::now()->format('Y-m-d'),
        ]);

        // Trigger email if status changed to 'To Evaluate' during an update/edit
        if ($evaluation->status === 'To Evaluate' && $oldStatus !== 'To Evaluate') {
            $this->sendEvaluatorEmail($evaluation);
        }

        // Audit log description
        $descriptionParts = [];
        foreach ($data as $key => $value) {
            if ($oldData[$key] != $value) {
                $descriptionParts[] = strtoupper($key) . " changed from '{$oldData[$key]}' to '$value'";
            }
        }
        $description = !empty($descriptionParts) ? implode(", ", $descriptionParts) : "Record updated with no value changes.";

        $this->logAudit('UPDATE', 'Evaluation Page', "User evaluated record: {$evaluation->refnum}. $description");

        return redirect()->route('evaluations')->with('success', 'Evaluation submitted successfully.');
    }

    private function sendEvaluatorEmail($evaluation)
    {
        try {
            if (!empty($evaluation->eemail)) {
                Mail::to($evaluation->eemail)->send(new EvaluationNotification($evaluation));
            }
        } catch (\Exception $e) {
            // Log error but don't stop the request
            \Illuminate\Support\Facades\Log::error("Failed to send evaluation email for {$evaluation->refnum}: " . $e->getMessage());
        }
    }

    private function logAudit($action, $page, $description)
    {
        $user = Auth::user();
        AuditTrail::create([
            'USER_ID' => $user->EmpNo,
            'USER_NAME' => $user->EmpName,
            'ACTION_TYPE' => $action,
            'PAGE_NAME' => $page,
            'DESCRIPTION' => $description,
            'IP_ADDRESS' => request()->ip(),
            'ADDDATE' => Carbon::now()->format('Y-m-d'),
            'ADDTIME' => Carbon::now()->format('H:i:s'),
        ]);
    }
}
