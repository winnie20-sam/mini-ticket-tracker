<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListTicketsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_tickets_by_status(): void
    {
        Ticket::create(['title' => 'Open 1', 'status' => 'open']);
        Ticket::create(['title' => 'Open 2', 'status' => 'open']);
        Ticket::create(['title' => 'Closed 1', 'status' => 'closed']);
        Ticket::create(['title' => 'Closed 2', 'status' => 'closed']);

        $response = $this->getJson('/tickets?status=closed');

        $response->assertJsonPath('status.code', 1)
            ->assertJsonPath('data.total', 2)
            ->assertJsonCount(2, 'data.tickets')
            ->assertJsonPath('data.tickets.0.status', 'closed')
            ->assertJsonPath('data.tickets.1.status', 'closed');
    }

    public function test_it_paginates_tickets_newest_first(): void
    {
        foreach ([1, 2, 3, 4, 5] as $number) {
            Ticket::create(['title' => "Ticket $number"]);
        }

        // 5 tickets, 2 per page: page 1 = 5, 4 / page 2 = 3, 2 / page 3 = 1
        $response = $this->getJson('/tickets?page=2&limit=2');

        $response->assertJsonPath('status.code', 1)
            ->assertJsonPath('data.current_page', 2)
            ->assertJsonPath('data.last_page', 3)
            ->assertJsonPath('data.total', 5)
            ->assertJsonPath('data.tickets.0.title', 'Ticket 3')
            ->assertJsonPath('data.tickets.1.title', 'Ticket 2');
    }
}
