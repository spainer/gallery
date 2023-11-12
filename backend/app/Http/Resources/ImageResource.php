<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'album' => new AlbumResource($this->whenLoaded('album')),
            'user' => new UserResource($this->whenLoaded('user')),
            'exifData' => ExifDataResource::collection($this->exifData),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'dateTime' => $this->image_date
        ];
    }
}
