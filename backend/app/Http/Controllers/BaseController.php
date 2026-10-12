<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Shared\CustomResponseController;
use App\Models\Shared\CustomConstants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public $resp, $const;

    /**
     * Base controller constructor
     */
    public function __construct()
    {
        $this->resp = new CustomResponseController();
        $this->const = new CustomConstants();
    }

    /**
     * Paginate data, newest first
     *
     * @param Request $request
     * @param $data
     * @param string $content
     * @return JsonResponse
     */
    public function paginate(Request $request, $data, string $content = 'items'): JsonResponse
    {
        $limit = (int) $request->input('limit', $this->const::DEFAULT_PAGE_SIZE);
        $page = (int) $request->input('page', 1);

        $data = $data->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($limit, ['*'], 'page', $page);

        $formattedData = [
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
            'last_page' => $data->lastPage(),
            'total' => $data->total(),
            $content => $data->items(),
        ];

        return $this->resp->response($this->const::RESPONSE_STATUS_SUCCESS, $this->const::RESPONSE_MESSAGE_SUCCESS, $formattedData);
    }
}
