<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_counts_tickets_by_status(): void
    {
        Ticket::create(['title' => 'A', 'status' => 'open']);
        Ticket::create(['title' => 'B', 'status' => 'open']);
        Ticket::create(['title' => 'C', 'status' => 'open']);
        Ticket::create(['title' => 'D', 'status' => 'closed']);

        $response = $this->getJson('/tickets/stats');

        // in_progress has no tickets but must still be returned as 0.
        $response->assertJsonPath('status.code', 1)
            ->assertJsonPath('data.by_status.open', 3)
            ->assertJsonPath('data.by_status.in_progress', 0)
            ->assertJsonPath('data.by_status.closed', 1)
            ->assertJsonPath('data.total', 4);
    }
}
