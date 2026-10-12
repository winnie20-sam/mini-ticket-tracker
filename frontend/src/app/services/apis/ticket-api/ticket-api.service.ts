import { Injectable } from '@angular/core';

import { HttpService } from '@service/http/http.service';

@Injectable({
  providedIn: 'root',
})
export class TicketApiService {
  constructor(private hs: HttpService) {}

  public searchTickets(params: any) {
    return this.hs.makeRequest('/tickets', 'GET', null, { params });
  }

  public createTicket(data: any) {
    return this.hs.makeRequest('/tickets', 'POST', data);
  }

  public updateTicket(id: number, data: any) {
    return this.hs.makeRequest(`/tickets/${id}`, 'PATCH', data);
  }

  public getStats() {
    return this.hs.makeRequest('/tickets/stats', 'GET');
  }
}
