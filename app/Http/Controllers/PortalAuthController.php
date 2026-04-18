<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('portal.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!in_array($user->role, ['Empresa', 'Funcionario'])) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'As credenciais informadas não possuem acesso ao portal.',
                ]);
            }

            return $this->redirectUser($user);
        }

        return back()->withErrors([
            'email' => 'As credenciais informadas não coincidem com nossos registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login');
    }

    protected function redirectUser($user)
    {
        if ($user->role === 'Empresa') {
            return redirect()->route('portal.company.dashboard');
        }
        return redirect()->route('portal.worker.dashboard');
    }
}
