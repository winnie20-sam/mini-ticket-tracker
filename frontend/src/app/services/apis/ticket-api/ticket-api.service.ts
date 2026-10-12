import { Injectable } from '@angular/core';
import { HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

import { HttpService } from '@service/http/http.service';
import {
  ApiResponse,
  Ticket,
  TicketPage,
  TicketPriority,
  TicketStats,
  TicketStatus,
} from '@model/ticket';

@Injectable({
  providedIn: 'root',
})
export class TicketApiService {
  constructor(private hs: HttpService) {}

  public searchTickets(page: number, limit: number, status?: TicketStatus | ''): Observable<ApiResponse<TicketPage>> {
    let params = new HttpParams().set('page', page).set('limit', limit);
    if (status) {
      params = params.set('status', status);
    }
    return this.hs.makeRequest('/tickets', 'GET', null, { params }) as Observable<ApiResponse<TicketPage>>;
  }

  public createTicket(data: { title: string; description?: string | null; priority: TicketPriority }): Observable<ApiResponse<Ticket>> {
    return this.hs.makeRequest('/tickets', 'POST', data) as Observable<ApiResponse<Ticket>>;
  }

  public updateTicket(id: number, data: { status?: TicketStatus; priority?: TicketPriority }): Observable<ApiResponse<Ticket>> {
    return this.hs.makeRequest(`/tickets/${id}`, 'PATCH', data) as Observable<ApiResponse<Ticket>>;
  }

  public getStats(): Observable<ApiResponse<TicketStats>> {
    return this.hs.makeRequest('/tickets/stats', 'GET') as Observable<ApiResponse<TicketStats>>;
  }
}
