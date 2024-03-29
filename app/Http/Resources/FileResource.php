<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public static $wrap = false;

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
            'path' => $this->path,
            'parent_id' => $this->parent_id,
            'is_directory' => $this->is_directory,
            'owner' => $this->owner,
            'mime' => $this->mime,
            'size' => $this->size,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'created_by' => $this->created_by,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
