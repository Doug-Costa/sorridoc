    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1a202c; padding: 20px; font-size: 12px; }
        .page-header { position: relative; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; height: 100px; }
        .header-text { margin-right: 160px; }
        .header-text h1 { color: #4f46e5; margin: 0; font-size: 22px; text-transform: uppercase; }
        .header-text p { color: #718096; margin: 5px 0 0; font-size: 14px; }
        
        .qr-seal { position: absolute; top: 0; right: 0; width: 120px; text-align: center; border: 1px solid #e2e8f0; padding: 10px; border-radius: 12px; background: #fff; }
        .qr-seal img { width: 100px; height: 100px; }
        .qr-seal span { display: block; font-size: 8px; color: #718096; margin-top: 5px; text-transform: uppercase; font-weight: bold; }

        .section { margin-bottom: 25px; clear: both; }
        .section-title { font-weight: bold; font-size: 13px; text-transform: uppercase; color: #4a5568; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }
        
        .field { margin-bottom: 8px; }
        .label { font-weight: bold; color: #4a5568; margin-right: 5px; }
        
        .description-box { background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; font-style: italic; color: #334155; }
        
        .decision-card { margin-top: 15px; padding: 15px; background-color: #fff; border-radius: 10px; border: 1px solid #e2e8f0; position: relative; }
        .status-badge { float: right; padding: 4px 12px; border-radius: 9999px; color: white; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        .status-aprovado { background-color: #059669; }
        .status-rejeitado { background-color: #dc2626; }
        
        .hash-code { font-family: 'Courier', monospace; font-size: 9px; color: #64748b; background: #f1f5f9; padding: 5px; border-radius: 4px; display: block; margin-top: 8px; }
        
        .footer { margin-top: 60px; border-top: 1px solid #e2e8f0; padding-top: 20px; font-size: 9px; color: #94a3b8; text-align: center; line-height: 1.5; }
        .integrity-seal { font-weight: bold; color: #475569; margin: 10px 0; font-size: 10px; }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="header-text">
            <h1>Atestado de Aprovação Eletrônica</h1>
            <p>SorriDoc — Plataforma de Conformidade</p>
        </div>
        <div class="qr-seal">
            <table style="border-collapse: collapse; margin: 0 auto; line-height: 0; background: #fff; padding: 2px;">
                @foreach ($qrMatrix as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td style="width: 2.2px; height: 2.2px; background: {{ $cell > 0 ? '#000' : '#fff' }}; padding: 0; border: none; font-size: 0; line-height: 0;"></td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
            <span>Escaneie para Verificar Veracidade</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Informações do Processo</div>
        <div class="field"><span class="label">Requisição ID:</span> #{{ str_pad($approval->id, 6, '0', STR_PAD_LEFT) }}</div>
        <div class="field"><span class="label">Título:</span> {{ $approval->title }}</div>
        <div class="field"><span class="label">Categoria:</span> {{ $approval->category }}</div>
        <div class="field"><span class="label">Solicitante:</span> {{ $approval->owner->name }}</div>
        <div class="field"><span class="label">Iniciado em:</span> {{ $approval->created_at->format('d/m/Y H:i:s') }}</div>
    </div>

    <div class="section">
        <div class="section-title">Objeto da Aprovação / Observações</div>
        <div class="description-box">
            {!! nl2br(e($approval->description)) !!}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Certificação de Decisão</div>
        @foreach($approval->approvalFlows as $flow)
            <div class="decision-card">
                <span class="status-badge {{ $flow->status === 'Aprovado' ? 'status-aprovado' : 'status-rejeitado' }}">
                    {{ $flow->status }}
                </span>
                <div class="field"><span class="label">Etapa:</span> {{ $flow->step_name }}</div>
                <div class="field"><span class="label">Assinado por:</span> {{ $flow->assignedUser->name ?? 'Sistema' }}</div>
                <div class="field"><span class="label">Data/Hora:</span> {{ $flow->approved_at ? $flow->approved_at->format('d/m/Y H:i:s') : '-' }}</div>
                @if($flow->comment)
                    <div class="field" style="margin-top: 5px;">
                        <span class="label">Parecer técnico:</span><br>
                        <span style="color: #475569;">{{ $flow->comment }}</span>
                    </div>
                @endif
                <div class="field">
                    <span class="label">Assinatura Digital (ID de Integridade):</span>
                    <span class="hash-code">{{ $flow->signature_hash }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="footer">
        <div class="integrity-seal">
            CHAVE ÚNICA DE VERIFICAÇÃO: {{ $approval->hash_sha256 }}
        </div>
        <p>Este documento possui validade jurídica como prova de manifestação de vontade eletrônica, em conformidade com as diretrizes internas de compliance da SorriDoc.</p>
        <p>A integridade deste documento pode ser validada a qualquer momento através do QR Code superior ou do link: <br> {{ route('approvals.verify', $approval->hash_sha256) }}</p>
        <p>Audit Timestamp: {{ now()->format('d/m/Y H:i:s') }} | Origin IP: {{ request()->ip() }}</p>
    </div>
</body>
</html>
