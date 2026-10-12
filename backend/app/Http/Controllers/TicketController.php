<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TicketController extends BaseController
{
    /**
     * Display a paginated listing of tickets, optionally filtered by status
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $valid = $this->validateListPayload($request);
            if ($valid->fails()) {
                return $this->resp->response($this->const::RESPONSE_STATUS_PAYLOAD_VALIDATION_FAIL, $valid->errors()->all(), '');
            }

            return $this->paginate($request, $this->searchRecords($request), 'tickets');
        } catch (Exception $e) {
            return $this->resp->response($this->const::RESPONSE_STATUS_FAILED, $this->const::RESPONSE_MESSAGE_FAILED, $e->getMessage());
        }
    }

    /**
     * Stores a newly created ticket in the database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $valid = $this->validatePayload($request);
            if ($valid->fails()) {
                return $this->resp->response($this->const::RESPONSE_STATUS_PAYLOAD_VALIDATION_FAIL, $valid->errors()->all(), '');
            }

            $ticket = Ticket::create($this->payload($request));

            return $this->resp->response($this->const::RESPONSE_STATUS_SUCCESS, $this->const::RESPONSE_MESSAGE_SUCCESS, $ticket->refresh());
        } catch (Exception $e) {
            return $this->resp->response($this->const::RESPONSE_STATUS_FAILED, $this->const::RESPONSE_MESSAGE_FAILED, $e->getMessage());
        }
    }

    /**
     * Updates a ticket's status and/or priority
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id)
    {
        try {
            $ticket = $this->getTicketById($id);
            if (! $ticket) {
                return $this->resp->response($this->const::RESPONSE_STATUS_RECORD_NOT_FOUND, $this->const::RESPONSE_MESSAGE_RECORD_NOT_FOUND, '');
            }

            $valid = $this->validateEditPayload($request);
            if ($valid->fails()) {
                return $this->resp->response($this->const::RESPONSE_STATUS_PAYLOAD_VALIDATION_FAIL, $valid->errors()->all(), '');
            }

            $ticket->update($this->editPayload($request));

            return $this->resp->response($this->const::RESPONSE_STATUS_SUCCESS, $this->const::RESPONSE_MESSAGE_SUCCESS, $ticket);
        } catch (Exception $e) {
            return $this->resp->response($this->const::RESPONSE_STATUS_FAILED, $this->const::RESPONSE_MESSAGE_FAILED, $e->getMessage());
        }
    }

    /**
     * Returns the number of tickets grouped by status
     *
     * @return JsonResponse
     */
    public function stats()
    {
        try {
            $counts = Ticket::query()
                ->toBase()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            // Every status is always returned, including those with zero tickets.
            $byStatus = [];
            foreach (TicketStatus::cases() as $status) {
                $byStatus[$status->value] = (int) ($counts[$status->value] ?? 0);
            }

            return $this->resp->response($this->const::RESPONSE_STATUS_SUCCESS, $this->const::RESPONSE_MESSAGE_SUCCESS, [
                'by_status' => $byStatus,
                'total' => array_sum($byStatus),
            ]);
        } catch (Exception $e) {
            return $this->resp->response($this->const::RESPONSE_STATUS_FAILED, $this->const::RESPONSE_MESSAGE_FAILED, $e->getMessage());
        }
    }

    /**
     * Builds the ticket list query
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function searchRecords(Request $request)
    {
        return Ticket::query()
            ->when($request->status, fn ($query, $status) => $query->where('status', $status));
    }

    /**
     * Get ticket by id
     *
     * @param $id
     * @return Ticket|null
     */
    private function getTicketById($id)
    {
        return Ticket::find($id);
    }

    /**
     * Payload request for creating a ticket
     *
     * @param Request $request
     * @return array
     */
    private function payload(Request $request)
    {
        return [
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? TicketStatus::Open->value,
            'priority' => $request->priority ?? TicketPriority::Medium->value,
        ];
    }

    /**
     * Payload request for updating a ticket (status and priority only)
     *
     * @param Request $request
     * @return array
     */
    private function editPayload(Request $request)
    {
        return $request->only(['status', 'priority']);
    }

    /**
     * Validate list query parameters
     *
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    private function validateListPayload(Request $request)
    {
        return Validator::make($request->all(), [
            'status' => ['sometimes', Rule::enum(TicketStatus::class)],
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:'.$this->const::MAX_PAGE_SIZE,
        ]);
    }

    /**
     * Validate create payload
     *
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    private function validatePayload(Request $request)
    {
        return Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'status' => ['sometimes', Rule::enum(TicketStatus::class)],
            'priority' => ['sometimes', Rule::enum(TicketPriority::class)],
        ]);
    }

    /**
     * Validate update payload: at least one of status or priority
     *
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     */
    private function validateEditPayload(Request $request)
    {
        return Validator::make($request->all(), [
            'status' => ['required_without:priority', Rule::enum(TicketStatus::class)],
            'priority' => ['required_without:status', Rule::enum(TicketPriority::class)],
        ]);
    }
}
