@extends('portal.layouts.app')

@section('title', 'Login')

@section('content')
<div class="auth-card">
    <h1>SorriMed</h1>
    
    @if(session('error'))
        <div class="error-msg">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('portal.login') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="error-msg" style="background: none; padding: 0; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Senha</label>
            <input type="password" name="password" id="password" class="form-input" required>
        </div>

        <button type="submit" class="btn-primary">Acessar Portal</button>
    </form>
    
    <div style="text-align: center; margin-top: 24px; font-size: 13px; color: var(--text-muted);">
        Acesso restrito a empresas e funcionários credenciados.
    </div>
</div>
@endsection
