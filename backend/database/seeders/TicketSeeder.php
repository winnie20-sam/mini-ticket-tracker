<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * A few hand-written tickets covering every status and priority.
     */
    public function run(): void
    {
        $tickets = [
            [
                'title' => 'Cannot log in to the customer portal',
                'description' => 'Users get "invalid session" right after entering their password.',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::High,
                'created_at' => now()->subDays(4),
            ],
            [
                'title' => 'Invoice PDF shows the wrong currency',
                'description' => 'Amounts are in KES but the label says USD.',
                'status' => TicketStatus::InProgress,
                'priority' => TicketPriority::Medium,
                'created_at' => now()->subDays(3),
            ],
            [
                'title' => 'Add dark mode to the dashboard',
                'description' => null,
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::Low,
                'created_at' => now()->subDays(2),
            ],
            [
                'title' => 'Password reset email not delivered',
                'description' => 'Fixed: the mail queue worker had stopped.',
                'status' => TicketStatus::Closed,
                'priority' => TicketPriority::High,
                'created_at' => now()->subDay(),
            ],
            [
                'title' => 'Typo on the pricing page',
                'description' => '"Anual" should be "Annual".',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::Low,
                'created_at' => now(),
            ],
        ];

        foreach ($tickets as $ticket) {
            // forceCreate so the back-dated created_at (not fillable via the API) is kept.
            Ticket::forceCreate($ticket);
        }
    }
}
