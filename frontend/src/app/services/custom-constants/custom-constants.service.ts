import { Injectable } from '@angular/core';

import { TicketPriority, TicketStatus } from '@model/ticket';

@Injectable({
  providedIn: 'root',
})
export class CustomConstantsService {
  // Response status codes (match the backend CustomConstants)
  RESPONSE_STATUS_SUCCESS = 1;
  RESPONSE_STATUS_FAILED = 2;
  RESPONSE_STATUS_PAYLOAD_VALIDATION_FAIL = 3;
  RESPONSE_STATUS_RECORD_NOT_FOUND = 4;

  // Ticket statuses and priorities, with display labels
  TICKET_STATUSES: { value: TicketStatus; label: string }[] = [
    { value: 'open', label: 'Open' },
    { value: 'in_progress', label: 'In progress' },
    { value: 'closed', label: 'Closed' },
  ];

  TICKET_PRIORITIES: { value: TicketPriority; label: string }[] = [
    { value: 'low', label: 'Low' },
    { value: 'medium', label: 'Medium' },
    { value: 'high', label: 'High' },
  ];

  DEFAULT_PAGE_SIZE = 10;
}
