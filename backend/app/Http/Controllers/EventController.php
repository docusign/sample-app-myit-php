<?php

namespace App\Http\Controllers;

use App\Services\DocuSign\EventBroadcastingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class EventController
 *
 * @package App\Http\Controllers
 */
class EventController extends BaseController
{
    /**
     * Events callback
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(Request $request): JsonResponse
    {
        $result = app(EventBroadcastingService::class)->send($request->all());

        return response()->json(['success' => $result]);
    }
}