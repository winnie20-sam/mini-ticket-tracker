import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

import { environment } from '@env/environment';

@Injectable({
  providedIn: 'root',
})
export class HttpService {
  constructor(private hc: HttpClient) {}

  public makeRequest(path: string, method: string, body?: any, options?: any): Observable<any> {
    return this.hc.request(method, `${environment.API_BASE_URL}${path}`, {
      body,
      ...options,
    });
  }
}
