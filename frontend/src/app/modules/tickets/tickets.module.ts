import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { TicketsRoutingModule } from './tickets-routing.module';
import { TicketsComponent } from './tickets/tickets.component';
import { TicketStatsComponent } from './ticket-stats/ticket-stats.component';
import { CreateTicketComponent } from './create-ticket/create-ticket.component';
import { TicketListComponent } from './ticket-list/ticket-list.component';

@NgModule({
  declarations: [TicketsComponent, TicketStatsComponent, CreateTicketComponent, TicketListComponent],
  imports: [CommonModule, FormsModule, ReactiveFormsModule, TicketsRoutingModule],
})
export class TicketsModule {}
