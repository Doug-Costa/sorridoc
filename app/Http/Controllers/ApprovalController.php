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
}
