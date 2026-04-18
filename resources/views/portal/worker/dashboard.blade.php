@extends('portal.layouts.app')

@section('title', 'Meus Documentos')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 style="font-size: 28px; margin-bottom: 8px;">Olá, {{ $worker->name }}</h1>
            <p style="color: var(--text-muted);">{{ $worker->role }} | CPF: {{ $worker->masked_cpf }}</p>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 14px; font-weight: 600;">{{ $worker->company->fantasy_name }}</p>
            <p style="font-size: 13px; color: var(--text-muted);">Portal do Colaborador</p>
        </div>
    </div>
</div>

<div class="card">
    <h2 class="card-title">Meus Documentos Ocupacionais</h2>
    <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px;">Abaixo estão os documentos emitidos pela SorriMed vinculados ao seu cadastro.</p>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Título / Descrição</th>
                    <th>Data de Emissão</th>
                    <th>Validade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td>
                            <span class="badge" style="background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                                {{ $doc->type }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $doc->title }}</div>
                            @if($doc->description)
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $doc->description }}</div>
                            @endif
                        </td>
                        <td>{{ $doc->issued_at?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td>
                            @if($doc->expires_at)
                                <span style="{{ $doc->is_expired ? 'color: #dc2626; font-weight: 600;' : '' }}">
                                    {{ $doc->expires_at->format('d/m/Y') }}
                                </span>
                                @if($doc->is_expired)
                                    <span class="badge badge-danger" style="margin-left: 4px; background: #fee2e2; color: #991b1b;">VENCIDO</span>
                                @endif
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">Não expira</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('portal.download', $doc) }}" class="btn-primary" style="padding: 8px 16px; font-size: 12px; text-decoration: none; display: inline-block; width: auto;">
                                Baixar Agora
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 60px; color: var(--text-muted);">
                            <div style="font-size: 40px; margin-bottom: 10px;">📄</div>
                            Você ainda não possui documentos disponíveis para download.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 40px; text-align: center; color: var(--text-muted); font-size: 13px;">
    Em caso de dúvidas sobre seus documentos, entre em contato com o RH da sua empresa.
</div>
@endsection
