@extends('portal.layouts.app')

@section('title', 'Dashboard Empresa')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 style="font-size: 28px; margin-bottom: 8px;">{{ $company->fantasy_name }}</h1>
            <p style="color: var(--text-muted);">{{ $company->corporate_name }} | CNPJ: {{ $company->cnpj }}</p>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 14px; font-weight: 600;">{{ $workers->count() }} Funcionários</p>
            <p style="font-size: 13px; color: var(--text-muted);">Painel de Gestão Ocupacional</p>
        </div>
    </div>
</div>

<div class="card">
    <h2 class="card-title">Gestão de Documentos Ocupacionais</h2>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Funcionário</th>
                    <th>CPF</th>
                    <th>Documentos Disponíveis</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workers as $worker)
                    <tr>
                        <td style="font-weight: 600;">{{ $worker->name }}</td>
                        <td>{{ $worker->masked_cpf }}</td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @forelse($worker->documents as $doc)
                                    <div style="display: flex; justify-content: space-between; align-items: center; background: #f9fafb; padding: 10px; border-radius: 6px;">
                                        <div>
                                            <span style="font-weight: 700; font-size: 12px; color: var(--primary);">{{ $doc->type }}</span>
                                            <span style="margin-left: 8px; font-size: 13px;">{{ $doc->title }}</span>
                                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                                Emissão: {{ $doc->issued_at?->format('d/m/Y') ?? 'N/A' }} 
                                                @if($doc->expires_at) 
                                                    | Vcto: {{ $doc->expires_at->format('d/m/Y') }} 
                                                    <span class="badge {{ $doc->is_expired ? 'badge-danger' : 'badge-success' }}" style="margin-left: 5px; font-size: 9px;">
                                                        {{ $doc->is_expired ? 'VENCIDO' : 'VÁLIDO' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('portal.download', $doc) }}" class="download-btn" title="Baixar Arquivo">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width:18px; height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Wait, I'll use text for now to avoid svg issues" />
                                                <text y="15" x="2" font-size="12" fill="currentColor">⬇</text>
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                @empty
                                    <span style="font-size: 12px; color: var(--text-muted); font-style: italic;">Nenhum documento pendente.</span>
                                @endforelse
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            Nenhum funcionário vinculado a esta empresa.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
