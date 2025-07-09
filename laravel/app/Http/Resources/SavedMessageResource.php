<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'from_user_id' => $this->from_user_id,
            'to_user_id' => $this->to_user_id,
            'reply_to' => $this->reply_to,
            'attachment' => $this->attachment ? asset('storage/'.$this->attachment) : null,
            'created_at' => $this->created_at,
            'is_edited' => $this->is_edited,
        ];
    }
}
