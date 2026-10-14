<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ToDoResource extends JsonResource
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
            'job' => $this->job,
            'description' => $this->description,
            'created_by' => $this->whenLoaded('creator',function(){
                return [
                    'id'=> $this->creator->id,
                    'name'=> $this->creator->name,
                    'email'=> $this->creator->email,
                ];
            }),
            'updated_by' => $this->whenLoaded('updater',function(){
                return [
                    'id'=> $this->updater->id,
                    'name'=> $this->updater->name,
                    'email'=> $this->updater->email,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
