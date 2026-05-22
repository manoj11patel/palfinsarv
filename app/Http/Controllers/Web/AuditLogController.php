<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->latest();

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }

        $auditLogs = $query->paginate(20);
        $actions = AuditLog::selectRaw('DISTINCT action')->orderBy('action')->pluck('action');
        $entityTypes = AuditLog::selectRaw('DISTINCT entity_type')->orderBy('entity_type')->pluck('entity_type');

        return view('admin.audit-logs.index', [
            'auditLogs' => $auditLogs,
            'actions' => $actions,
            'entityTypes' => $entityTypes,
        ]);
    }
}
