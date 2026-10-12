import { Component, EventEmitter, Output } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';

import { TicketPriority } from '@model/ticket';
import { TicketApiService } from '@service/apis/ticket-api/ticket-api.service';
import { CustomConstantsService } from '@service/custom-constants/custom-constants.service';

@Component({
  selector: 'app-create-ticket',
  templateUrl: './create-ticket.component.html',
  styleUrl: './create-ticket.component.scss',
})
export class CreateTicketComponent {
  @Output() created = new EventEmitter<void>();

  submitting = false;
  errorMessages: string[] = [];

  form;

  constructor(
    private fb: FormBuilder,
    private api: TicketApiService,
    public constants: CustomConstantsService,
    private toastr: ToastrService
  ) {
    // nonNullable: reset() restores these defaults (priority back to medium).
    this.form = this.fb.nonNullable.group({
      title: ['', [Validators.required, Validators.maxLength(255)]],
      description: ['', [Validators.maxLength(5000)]],
      priority: ['medium' as TicketPriority, [Validators.required]],
    });
  }

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.submitting = true;
    this.errorMessages = [];
    const { title, description, priority } = this.form.getRawValue();

    this.api.createTicket({ title: title.trim(), description: description.trim() || null, priority }).subscribe({
      next: (res) => {
        this.submitting = false;
        if (res.status.code === this.constants.RESPONSE_STATUS_SUCCESS) {
          this.toastr.success(`Ticket #${res.data.id} created`);
          this.form.reset();
          this.created.emit();
        } else {
          this.errorMessages = ([] as string[]).concat(res.status.message);
        }
      },
      error: () => {
        this.submitting = false;
        this.toastr.error('Could not reach the server', 'Ticket not created');
      },
    });
  }
}
