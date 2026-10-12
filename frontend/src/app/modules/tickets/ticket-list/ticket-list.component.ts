import { Component, EventEmitter, OnInit, Output } from '@angular/core';
import { ToastrService } from 'ngx-toastr';

import { Ticket, TicketPage, TicketPriority, TicketStatus } from '@model/ticket';
import { TicketApiService } from '@service/apis/ticket-api/ticket-api.service';
import { CustomConstantsService } from '@service/custom-constants/custom-constants.service';

@Component({
  selector: 'app-ticket-list',
  templateUrl: './ticket-list.component.html',
  styleUrl: './ticket-list.component.scss',
})
export class TicketListComponent implements OnInit {
  @Output() updated = new EventEmitter<void>();

  page: TicketPage | null = null;
  statusFilter: TicketStatus | '' = '';
  loading = false;
  savingId: number | null = null;

  constructor(
    private api: TicketApiService,
    public constants: CustomConstantsService,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.searchTickets(1);
  }

  searchTickets(page: number): void {
    this.loading = true;

    const params: any = { page: page, limit: this.constants.DEFAULT_PAGE_SIZE };
    if (this.statusFilter) {
      params.status = this.statusFilter;
    }

    this.api.searchTickets(params).subscribe({
      next: (res) => {
        this.loading = false;
        if (res.status.code === this.constants.RESPONSE_STATUS_SUCCESS) {
          this.page = res.data;
        } else {
          this.toastr.error(String(res.status.message), 'Could not load tickets');
        }
      },
      error: () => {
        this.loading = false;
        this.toastr.error('Could not reach the server', 'Could not load tickets');
      },
    });
  }

  onFilterChange(): void {
    this.searchTickets(1);
  }

  changeStatus(ticket: Ticket, status: TicketStatus): void {
    const previous = ticket.status;
    ticket.status = status;
    this.savingId = ticket.id;

    this.api.updateTicket(ticket.id, { status }).subscribe({
      next: (res) => {
        this.savingId = null;
        if (res.status.code === this.constants.RESPONSE_STATUS_SUCCESS) {
          this.toastr.success(`Ticket #${ticket.id} is now ${this.statusLabel(status)}`);
          this.updated.emit();
          // With a status filter on, the ticket may no longer belong on this page.
          if (this.statusFilter) {
            this.searchTickets(this.page?.current_page ?? 1);
          }
        } else {
          ticket.status = previous;
          this.toastr.error(String(res.status.message), 'Status not changed');
        }
      },
      error: () => {
        this.savingId = null;
        ticket.status = previous;
        this.toastr.error('Could not reach the server', 'Status not changed');
      },
    });
  }

  statusLabel(status: TicketStatus): string {
    return this.constants.TICKET_STATUSES.find((s) => s.value === status)?.label ?? status;
  }

  priorityLabel(priority: TicketPriority): string {
    return this.constants.TICKET_PRIORITIES.find((p) => p.value === priority)?.label ?? priority;
  }
}
