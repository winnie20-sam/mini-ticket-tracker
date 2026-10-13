<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_ticket_with_default_status_and_priority(): void
    {
        $response = $this->postJson('/tickets', ['title' => 'Printer jammed']);

        $response->assertJsonPath('status.code', 1)
            ->assertJsonPath('data.title', 'Printer jammed')
            ->assertJsonPath('data.status', 'open')
            ->assertJsonPath('data.priority', 'medium');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Printer jammed',
            'status' => 'open',
            'priority' => 'medium',
        ]);
    }

    public function test_it_rejects_a_ticket_without_a_title(): void
    {
        $response = $this->postJson('/tickets', ['priority' => 'urgent']);

        $response->assertJsonPath('status.code', 3)
            ->assertJsonPath('status.message', [
                'The title field is required.',
                'The selected priority is invalid.',
            ]);

        $this->assertDatabaseCount('tickets', 0);
    }
}
