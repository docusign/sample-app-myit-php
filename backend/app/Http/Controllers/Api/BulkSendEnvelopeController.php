<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\SendEnvelopeRequest;
use App\Services\CurrentUser;
use App\Services\DocuSign\BulkSendingEnvelopeService;
use DocuSign\eSign\Client\ApiException;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class BulkSendEnvelopeController
 *
 * @package App\Http\Controllers\Api
 */
class BulkSendEnvelopeController extends BaseController
{
    /**
     * Bulk envelope action
     *
     * @param SendEnvelopeRequest $request
     * @return JsonResponse
     * @throws ApiException
     * @throws Exception
     */
    public function __invoke(SendEnvelopeRequest $request): JsonResponse
    {
        $bulkService = new BulkSendingEnvelopeService(app(CurrentUser::class)->getTokenId());
        $bulkService->send($request->get('employees'));

        return response()->json(['success' => true]);
    }
}