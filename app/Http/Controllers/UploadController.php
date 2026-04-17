<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class UploadController extends Controller
{
    public function showLogin(string $token)
    {
        $company = Company::where('registration_token', hash('sha256', $token))->first();

        if (! $company) {
            abort(404, 'Token de acesso inválido.');
        }

        if (! $company->isTokenValid($token)) {
            abort(403, 'Token expirado. Solicite um novo à SorriDoc.');
        }

        return view('portals.upload.login', compact('company', 'token'));
    }

    public function authenticate(Request $request, string $token)
    {
        $company = Company::where('registration_token', hash('sha256', $token))->first();

        if (! $company || ! $company->isTokenValid($token)) {
            abort(403, 'Token inválido ou expirado.');
        }

        $key = 'upload-login:'.$company->id.':'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Muitas tentativas. Tente novamente em {$seconds} segundos.",
            ]);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('company_id', $company->id)
            ->whereIn('role', ['Gestor RH'])
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);

            return back()->withErrors([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        RateLimiter::clear($key);
        $user->recordAccess();

        session([
            'upload_company_id' => $company->id,
            'upload_user_id' => $user->id,
            'upload_token' => $token,
        ]);

        return redirect()->route('upload.index', $token);
    }

    public function index(string $token)
    {
        $company = $this->validateToken($token);

        $workers = $company->workers()
            ->where('status', 'Ativo')
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('portals.upload.index', compact('company', 'workers', 'token'));
    }

    public function store(Request $request, string $token)
    {
        $company = $this->validateToken($token);
        $user = $this->validateUploadSession($token);

        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'type' => 'required|in:'.implode(',', array_keys(WorkerDocument::TYPES)),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:102400',
            'issued_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:issued_at',
        ]);

        $worker = Worker::findOrFail($request->worker_id);

        if ($worker->company_id !== $company->id) {
            abort(403, 'Trabalhador não pertence à esta empresa.');
        }

        $file = $request->file('file');
        $path = $file->store('worker-documents', 'private');
        $originalName = $file->getClientOriginalName();

        $document = WorkerDocument::create([
            'worker_id' => $request->worker_id,
            'uploaded_by' => $user->id,
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'original_name' => $originalName,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'hash_sha256' => hash_file('sha256', $file->getRealPath()),
            'issued_at' => $request->issued_at,
            'expires_at' => $request->expires_at,
            'status' => 'Aprovado',
        ]);

        return back()->with('success', 'Documento enviado com sucesso!');
    }

    public function logout(string $token)
    {
        session()->forget(['upload_company_id', 'upload_user_id', 'upload_token']);

        return redirect()->route('upload.login', $token);
    }

    protected function validateToken(string $token)
    {
        $company = Company::where('registration_token', hash('sha256', $token))->first();

        if (! $company || ! $company->isTokenValid($token)) {
            abort(403, 'Token expirado. Faça login novamente.');
        }

        return $company;
    }

    protected function validateUploadSession(string $token)
    {
        if (session('upload_token') !== $token) {
            abort(403, 'Sessão inválida. Faça login novamente.');
        }

        return User::findOrFail(session('upload_user_id'));
    }
}
