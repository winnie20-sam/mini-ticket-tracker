import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';

import { TicketStats } from '@model/ticket';
import { TicketApiService } from '@service/apis/ticket-api/ticket-api.service';
import { CustomConstantsService } from '@service/custom-constants/custom-constants.service';

@Component({
  selector: 'app-ticket-stats',
  templateUrl: './ticket-stats.component.html',
  styleUrl: './ticket-stats.component.scss',
})
export class TicketStatsComponent implements OnInit {
  stats: TicketStats | null = null;

  constructor(
    private api: TicketApiService,
    public constants: CustomConstantsService,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.getStats();
  }

  getStats(): void {
    this.api.getStats().subscribe({
      next: (res) => {
        if (res.status.code === this.constants.RESPONSE_STATUS_SUCCESS) {
          this.stats = res.data;
        } else {
          this.toastr.error(String(res.status.message), 'Could not load stats');
        }
      },
      error: () => this.toastr.error('Could not reach the server', 'Could not load stats'),
    });
  }
}
