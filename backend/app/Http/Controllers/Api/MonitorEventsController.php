<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\CurrentUser;
use App\Services\DocuSign\EventResolver;
use DocuSign\Monitor\Client\ApiException;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class MonitorEventsController
 *
 * @package App\Http\Controllers\Api
 */
class MonitorEventsController extends BaseController
{
    /**
     * Get monitor alerts
     *
     * @return JsonResponse
     * @throws ApiException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(): JsonResponse
    {
        /** @var EventResolver $alertsService */
        $alertsService = app(
            EventResolver::class,
            [
                'tokenId' => app(CurrentUser::class)->getTokenId(),
            ]
        );

        return response()->json($alertsService->get());
    }
}