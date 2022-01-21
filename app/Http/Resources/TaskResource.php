<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        Log::channel('single_new')->info($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'card_id' => $this->card_id,
            'is_done' => $this->is_done,
            'created_at' => $this->created_at,
        ];
    }
}
