export type TicketStatus = 'open' | 'in_progress' | 'closed';
export type TicketPriority = 'low' | 'medium' | 'high';

export interface Ticket {
  id: number;
  title: string;
  description: string | null;
  status: TicketStatus;
  priority: TicketPriority;
  created_at: string;
  updated_at: string;
}

/** Every API response: { status: { code, message }, data } */
export interface ApiResponse<T> {
  status: {
    code: number;
    message: string | string[];
  };
  data: T;
}

export interface TicketPage {
  current_page: number;
  per_page: number;
  last_page: number;
  total: number;
  tickets: Ticket[];
}

export interface TicketStats {
  by_status: Record<TicketStatus, number>;
  total: number;
}
