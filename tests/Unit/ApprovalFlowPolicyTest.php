<?php

namespace Tests\Unit;

use App\Models\Approval;
use App\Models\ApprovalFlow;
use App\Models\User;
use App\Policies\ApprovalFlowPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalFlowPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_operacional_can_view_flow_if_owner_or_assigned(): void
    {
        $policy = new ApprovalFlowPolicy();
        
        $owner = User::factory()->create(['role' => 'Operacional']);
        $assigned = User::factory()->create(['role' => 'Operacional']);
        $other = User::factory()->create(['role' => 'Operacional']);
        
        $approval = Approval::create([
            'title' => 'Test',
            'category' => 'Contrato',
            'owner_id' => $owner->id,
            'assigned_to' => $assigned->id,
            'status' => 'Pendente',
            'deadline_at' => now(),
            'flow_type' => 'Simples'
        ]);

        $flow = new ApprovalFlow(['approval_id' => $approval->id]);
        $flow->setRelation('approval', $approval);

        $this->assertTrue($policy->view($owner, $flow));
        $this->assertTrue($policy->view($assigned, $flow));
        $this->assertFalse($policy->view($other, $flow));
    }

    public function test_super_admin_can_view_any_flow(): void
    {
        $policy = new ApprovalFlowPolicy();
        $admin = User::factory()->create(['role' => 'Super Admin']);
        $flow = new ApprovalFlow();

        $this->assertTrue($policy->view($admin, $flow));
    }
}
