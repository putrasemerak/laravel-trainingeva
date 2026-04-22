<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Response;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditTrail::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('USER_ID', 'LIKE', "%$search%")
                  ->orWhere('USER_NAME', 'LIKE', "%$search%")
                  ->orWhere('ACTION_TYPE', 'LIKE', "%$search%")
                  ->orWhere('PAGE_NAME', 'LIKE', "%$search%")
                  ->orWhere('DESCRIPTION', 'LIKE', "%$search%");
            });
        }

        if ($request->filled('start_date')) {
            $query->where('ADDDATE', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('ADDDATE', '<=', $request->end_date);
        }

        $logs = $query->orderBy('ADDDATE', 'DESC')->orderBy('ADDTIME', 'DESC')->paginate(100);

        return view('admin.audit_trail', compact('logs'));
    }

    public function export(Request $request)
    {
        $selectedIds = $request->selected_records;
        if (!$selectedIds) {
            return back()->with('error', 'No records selected for export.');
        }

        $logs = AuditTrail::whereIn('ID', $selectedIds)->orderBy('ADDDATE', 'DESC')->orderBy('ADDTIME', 'DESC')->get();

        $filename = "audit_trail_export_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'User ID', 'User Name', 'Action', 'Page', 'Description', 'IP Address', 'Date', 'Time'];

        $callback = function() use($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->USER_ID,
                    $row->USER_NAME,
                    $row->ACTION_TYPE,
                    $row->PAGE_NAME,
                    $row->DESCRIPTION,
                    $row->IP_ADDRESS,
                    $row->ADDDATE,
                    $row->ADDTIME
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
