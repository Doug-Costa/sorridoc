<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\User;
use App\Domain\Services\ApprovalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected ApprovalService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ApprovalService();
    }

    public function test_can_approve_simple_flow(): void
    {
        $user = User::factory()->create([
            'pin_code' => Hash::make('1234'),
            'role' => 'Operacional'
        ]);
        
        $this->actingAs($user);

        $approval = Approval::create([
            'title' => 'Test Approval',
            'category' => 'Contrato',
            'status' => 'Pendente',
            'owner_id' => $user->id,
            'assigned_to' => $user->id, // Assigned to self
            'deadline_at' => now()->addDays(7),
            'flow_type' => 'Simples'
        ]);

        $this->service->approve($approval, '1234', 'Approved');

        $this->assertEquals('Aprovado', $approval->fresh()->status);
        $this->assertCount(1, $approval->approvalFlows);
    }

    public function test_fails_if_not_assigned(): void
    {
        $owner = User::factory()->create();
        $assigned = User::factory()->create();
        $other = User::factory()->create(['pin_code' => Hash::make('1234')]);
        
        $approval = Approval::create([
            'title' => 'Test Approval',
            'category' => 'Contrato',
            'status' => 'Pendente',
            'owner_id' => $owner->id,
            'assigned_to' => $assigned->id,
            'deadline_at' => now()->addDays(7),
            'flow_type' => 'Simples'
        ]);

        $this->actingAs($other);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Apenas o usuário atribuído ou um Super Admin pode realizar esta assinatura.');
        
        $this->service->approve($approval, '1234', 'Approved');
    }

    public function test_dupla_flow_requirements(): void
    {
        $solicitante = User::factory()->create(['role' => 'Operacional']);
        $diretor = User::factory()->create(['pin_code' => Hash::make('1111'), 'role' => 'Diretor']);
        $advogada = User::factory()->create(['pin_code' => Hash::make('2222'), 'role' => 'Advogado']);

        $approval = Approval::create([
            'title' => 'Test Dupla',
            'category' => 'Compliance',
            'status' => 'Pendente',
            'owner_id' => $solicitante->id,
            'assigned_to' => $diretor->id, // 1st Step assigned to Diretor
            'deadline_at' => now()->addDays(7),
            'flow_type' => 'Dupla'
        ]);

        // 1. Solicitante não pode assinar o primeiro passo se não for o assigned_to
        $this->actingAs($solicitante);
        try {
            $this->service->approve($approval, 'any', 'Ok');
            $this->fail('Expected exception for wrong assignment');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Apenas o usuário atribuído ou um Super Admin pode realizar esta assinatura.', $e->getMessage());
        }

        // 2. Diretor assina o primeiro passo (ele é o assigned_to)
        $this->actingAs($diretor);
        $this->service->approve($approval, '1111', 'Ok Diretor');
        $this->assertEquals('Em Aprovação', $approval->fresh()->status);

        // 3. Advogada assina o passo final (regra por role para o 2º passo)
        $this->actingAs($advogada);
        $this->service->approve($approval, '2222', 'Ok Advogada');
        $this->assertEquals('Aprovado', $approval->fresh()->status);
    }
}
