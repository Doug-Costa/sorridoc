<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ApprovalController extends Controller
{
    public function downloadCertificate(Approval $approval)
    {
        $hash = $approval->hash_sha256 ?? md5($approval->id . 'fallback');
        $url = route('approvals.verify', ['hash' => $hash]);

        // Geramos uma matriz do QR Code (dispensa o uso da extensão GD pois não gera imagem)
        $options = new \chillerlan\QRCode\QROptions;
        $options->version = 4;
        $options->eccLevel = \chillerlan\QRCode\Common\EccLevel::L;

        $qrcode = new \chillerlan\QRCode\QRCode($options);
        $matrix = $qrcode->getQRMatrix()->getMatrix();

        $pdf = Pdf::loadView('pdf.approval_certificate', [
            'approval' => $approval->load(['owner', 'approvalFlows.assignedUser']),
            'qrMatrix' => $matrix
        ])->setPaper('a4')
          ->setOption('isHtml5ParserEnabled', true);

        return $pdf->download("certificado_sorridoc_{$approval->id}.pdf");
    }

    public function verify(string $hash)
    {
        $approval = Approval::where('hash_sha256', $hash)->firstOrFail();

        return view('pages.verify_approval', compact('approval'));
    }

    public function viewDocument(Approval $approval)
    {
        $user = auth()->user();
        
        // Verificar permissão: usuário atribuído, dono, ou Super Admin
        if ($user->id !== $approval->assigned_to && 
            $user->id !== $approval->owner_id && 
            $user->role !== 'Super Admin') {
            abort(403, 'Você não tem permissão para visualizar este documento.');
        }

        // Verificar se existe arquivo anexo
        if (!$approval->file_path) {
            abort(404, 'Nenhum arquivo anexado a esta aprovação.');
        }

        $filePath = storage_path('app/private/' . $approval->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        // Verificar se é um PDF
        $mimeType = mime_content_type($filePath);
        if ($mimeType !== 'application/pdf') {
            abort(422, 'Apenas arquivos PDF podem ser visualizados.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}
