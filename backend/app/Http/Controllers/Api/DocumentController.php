<?php

namespace App\Http\Controllers\Api;

use App\Enums\DocumentItemTypesEnum;
use App\Http\Controllers\BaseController;
use App\Http\Resources\DocumentItemResource;
use App\Models\DocumentItem;
use Illuminate\Http\JsonResponse;

/**
 * Class DocumentController
 *
 * @package App\Http\Controllers\Api
 */
class DocumentController extends BaseController
{
    /**
     * Getting documents action
     *
     * @param DocumentItem $document
     * @return JsonResponse
     */
    public function __invoke(DocumentItem $document): JsonResponse
    {
        return response()->json([
            'equipments' => DocumentItemResource::collection($document->getByType(DocumentItemTypesEnum::EQUIPMENT)),
            'software'   => DocumentItemResource::collection($document->getByType(DocumentItemTypesEnum::SOFTWARE)),
        ]);
    }
}