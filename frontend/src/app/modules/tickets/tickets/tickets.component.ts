import { Component, ViewChild } from '@angular/core';

import { TicketListComponent } from '../ticket-list/ticket-list.component';
import { TicketStatsComponent } from '../ticket-stats/ticket-stats.component';

@Component({
  selector: 'app-tickets',
  templateUrl: './tickets.component.html',
  styleUrl: './tickets.component.scss',
})
export class TicketsComponent {
  @ViewChild(TicketStatsComponent) stats!: TicketStatsComponent;
  @ViewChild(TicketListComponent) list!: TicketListComponent;

  /** A new ticket appears on page 1 (newest first), so jump back there. */
  onTicketCreated(): void {
    this.stats.getStats();
    this.list.searchTickets(1);
  }

  onTicketUpdated(): void {
    this.stats.getStats();
  }
}
