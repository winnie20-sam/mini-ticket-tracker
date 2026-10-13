<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_the_status_only(): void
    {
        $ticket = Ticket::create(['title' => 'Original title', 'status' => 'open']);

        // The title should be ignored: only status and priority can be changed.
        $response = $this->patchJson("/tickets/{$ticket->id}", [
            'status' => 'in_progress',
            'title' => 'Changed title',
        ]);

        $response->assertJsonPath('status.code', 1)
            ->assertJsonPath('data.status', 'in_progress')
            ->assertJsonPath('data.title', 'Original title');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Original title',
            'status' => 'in_progress',
        ]);
    }

    public function test_it_returns_record_not_found_for_a_missing_ticket(): void
    {
        $response = $this->patchJson('/tickets/999', ['status' => 'closed']);

        $response->assertJsonPath('status.code', 4)
            ->assertJsonPath('status.message', 'Record not found');
    }
}
