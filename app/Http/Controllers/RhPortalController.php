<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class RhPortalController extends Controller
{
    public function showLogin(string $token)
    {
        $company = Company::where('registration_token', hash('sha256', $token))->first();

        if (! $company) {
            abort(404, 'Token de acesso inválido ou expirado.');
        }

        if (! $company->isTokenValid($token)) {
            abort(403, 'Este token de acesso expirou. Solicite um novo token à SorriDoc.');
        }

        return view('portals.rh.login', compact('company', 'token'));
    }

    public function authenticate(Request $request, string $token)
    {
        $company = Company::where('registration_token', hash('sha256', $token))->first();

        if (! $company || ! $company->isTokenValid($token)) {
            abort(403, 'Token de acesso inválido ou expirado.');
        }

        $key = 'rh-login:'.$company->id.':'.$request->ip();

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
            ->where('role', 'Gestor RH')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);

            return back()->withErrors([
                'email' => 'Credenciais inválidas ou você não tem permissão de Gestor RH.',
            ]);
        }

        RateLimiter::clear($key);
        $user->recordAccess();

        session([
            'rh_company_id' => $company->id,
            'rh_user_id' => $user->id,
            'rh_token' => $token,
        ]);

        return redirect()->route('rh.dashboard', $token);
    }

    public function dashboard(string $token)
    {
        $company = $this->validateToken($token);

        $workers = $company->workers()
            ->withCount('documents')
            ->with(['documents' => function ($q) {
                $q->orderByDesc('created_at')->limit(1);
            }])
            ->orderBy('name')
            ->paginate(20);

        return view('portals.rh.dashboard', compact('company', 'workers', 'token'));
    }

    public function workers(string $token)
    {
        $company = $this->validateToken($token);

        $workers = $company->workers()
            ->withCount('documents')
            ->orderBy('name')
            ->get();

        return view('portals.rh.workers', compact('company', 'workers', 'token'));
    }

    public function showWorker(string $token, Worker $worker)
    {
        $company = $this->validateToken($token);

        if ($worker->company_id !== $company->id) {
            abort(403, 'Trabalhador não pertence à esta empresa.');
        }

        $documents = $worker->documents()
            ->with('uploader')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('type');

        return view('portals.rh.worker-detail', compact('company', 'worker', 'documents', 'token'));
    }

    public function logout(string $token)
    {
        session()->forget(['rh_company_id', 'rh_user_id', 'rh_token']);

        return redirect()->route('rh.login', $token);
    }

    protected function validateToken(string $token)
    {
        $company = Company::where('registration_token', hash('sha256', $token))->first();

        if (! $company || ! $company->isTokenValid($token)) {
            abort(403, 'Sessão expirada. Faça login novamente.');
        }

        if (session('rh_token') !== $token) {
            abort(403, 'Token de sessão inválido.');
        }

        return $company;
    }
}
