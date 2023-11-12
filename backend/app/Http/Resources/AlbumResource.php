<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
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
            'slug' => $this->slug,
            'preview' => new ImageResource($this->images()->orderByDesc('image_date')->first()),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'imagesCount' => $this->images->count(),
            'lastChanged' => $this->updated_at ??= $this->created_at
        ];
    }
}
