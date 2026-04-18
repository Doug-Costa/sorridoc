<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Worker;
use App\Models\WorkerDocument;

class PortalController extends Controller
{
    public function companyDashboard()
    {
        $user = Auth::user();
        if ($user->role !== 'Empresa') {
            return redirect()->route('portal.worker.dashboard');
        }

        $company = $user->company;
        $workers = $company->workers()->with(['documents' => function($q) {
            $q->orderBy('issued_at', 'desc');
        }])->get();

        return view('portal.company.dashboard', compact('company', 'workers'));
    }

    public function workerDashboard()
    {
        $user = Auth::user();
        if ($user->role !== 'Funcionario') {
            return redirect()->route('portal.company.dashboard');
        }

        $worker = $user->worker;
        $documents = $worker->documents()->orderBy('issued_at', 'desc')->get();

        return view('portal.worker.dashboard', compact('worker', 'documents'));
    }

    public function downloadDocument(WorkerDocument $document)
    {
        $user = Auth::user();
        
        // Verificação de permissão
        if ($user->role === 'Funcionario') {
            if ($document->worker_id !== $user->worker_id) {
                abort(403);
            }
        } elseif ($user->role === 'Empresa') {
            if ($document->worker->company_id !== $user->company_id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        if (!\Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Arquivo não encontrado no servidor.');
        }

        return \Storage::disk('public')->download($document->file_path, $document->original_name);
    }
}
