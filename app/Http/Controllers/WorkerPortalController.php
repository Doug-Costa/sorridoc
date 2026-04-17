<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Models\WorkerDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class WorkerPortalController extends Controller
{
    public function showLogin()
    {
        return view('portals.worker.login');
    }

    public function authenticate(Request $request)
    {
        $key = 'worker-login:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'cpf' => "Muitas tentativas. Tente novamente em {$seconds} segundos.",
            ]);
        }

        $validator = Validator::make($request->all(), [
            'cpf' => 'required|cpf',
        ], [
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.cpf' => 'CPF inválido.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $cpf = Worker::formatCpf($request->cpf);
        $worker = Worker::where('cpf', $cpf)->first();

        if (! $worker) {
            RateLimiter::hit($key, 60);

            return back()->withErrors([
                'cpf' => 'CPF não encontrado em nosso sistema.',
            ])->withInput();
        }

        if (! $worker->access_token || ! $worker->token_expires_at || ! $worker->token_expires_at->isFuture()) {
            return back()->withErrors([
                'cpf' => 'Acesso não liberado. Solicite acesso à SorriDoc através de sua empresa.',
            ])->withInput();
        }

        $token = $request->access_token;

        if (! $token) {
            session(['worker_cpf' => $cpf, 'worker_pending_token' => true]);

            return redirect()->route('worker.verify-token');
        }

        if (! $worker->isTokenValid($token)) {
            RateLimiter::hit($key, 60);

            return back()->withErrors([
                'access_token' => 'Token de acesso inválido ou expirado.',
            ]);
        }

        RateLimiter::clear($key);
        $worker->recordAccess();

        session([
            'worker_id' => $worker->id,
            'worker_cpf' => $cpf,
            'worker_token' => $token,
        ]);

        return redirect()->route('worker.dashboard');
    }

    public function showTokenVerification()
    {
        if (! session('worker_pending_token')) {
            return redirect()->route('worker.login');
        }

        return view('portals.worker.verify-token');
    }

    public function verifyToken(Request $request)
    {
        $cpf = session('worker_cpf');
        $worker = Worker::where('cpf', $cpf)->first();

        if (! $worker) {
            return redirect()->route('worker.login');
        }

        $token = $request->access_token;

        if (! $worker->isTokenValid($token)) {
            return back()->withErrors([
                'access_token' => 'Token de acesso inválido ou expirado.',
            ]);
        }

        $worker->recordAccess();

        session([
            'worker_id' => $worker->id,
            'worker_token' => $token,
            'worker_pending_token' => false,
        ]);

        return redirect()->route('worker.dashboard');
    }

    public function dashboard()
    {
        $worker = $this->validateSession();

        $documents = $worker->documents()
            ->with('uploader')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('type');

        return view('portals.worker.dashboard', compact('worker', 'documents'));
    }

    public function download(WorkerDocument $document)
    {
        $worker = $this->validateSession();

        if ($document->worker_id !== $worker->id) {
            abort(403, 'Documento não pertence a este trabalhador.');
        }

        $path = storage_path('app/private/'.$document->file_path);

        if (! file_exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return response()->download($path, $document->original_name);
    }

    public function logout()
    {
        session()->forget(['worker_id', 'worker_cpf', 'worker_token', 'worker_pending_token']);

        return redirect()->route('worker.login');
    }

    protected function validateSession()
    {
        $workerId = session('worker_id');
        $cpf = session('worker_cpf');
        $token = session('worker_token');

        if (! $workerId || ! $cpf || ! $token) {
            abort(403, 'Sessão expirada. Faça login novamente.');
        }

        $worker = Worker::where('id', $workerId)
            ->where('cpf', $cpf)
            ->first();

        if (! $worker || ! $worker->isTokenValid($token)) {
            session()->forget(['worker_id', 'worker_cpf', 'worker_token', 'worker_pending_token']);
            abort(403, 'Sessão expirada. Faça login novamente.');
        }

        return $worker;
    }
}
