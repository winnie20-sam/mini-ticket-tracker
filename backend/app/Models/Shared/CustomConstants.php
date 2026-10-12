<?php

namespace App\Models\Shared;

class CustomConstants
{
    // Response status codes
    const RESPONSE_STATUS_SUCCESS = 1;
    const RESPONSE_STATUS_FAILED = 2;
    const RESPONSE_STATUS_PAYLOAD_VALIDATION_FAIL = 3;
    const RESPONSE_STATUS_RECORD_NOT_FOUND = 4;

    // Response messages
    const RESPONSE_MESSAGE_SUCCESS = 'Success';
    const RESPONSE_MESSAGE_FAILED = 'Failed';
    const RESPONSE_MESSAGE_PAYLOAD_VALIDATION_FAIL = 'Data validation failed';
    const RESPONSE_MESSAGE_RECORD_NOT_FOUND = 'Record not found';

    // Pagination
    const DEFAULT_PAGE_SIZE = 10;
    const MAX_PAGE_SIZE = 100;
}
