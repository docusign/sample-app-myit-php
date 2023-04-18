<?php

namespace App\Http\Resources;

use App\Models\DocumentItem;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class DocumentItemResource
 *
 * @mixin DocumentItem $document
 *
 * @package App\Http\Resources
 */
class DocumentItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}