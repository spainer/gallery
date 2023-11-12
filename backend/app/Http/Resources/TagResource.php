<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent' => $this->whenLoaded('parent', new TagResource($this->parent()->first()), $this->parent),
            'children' => TagResource::collection($this->whenLoaded('children')),
            'childrenCount' => $this->children->count(),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'imagesCount' => $this->images->count()
        ];
    }
}
