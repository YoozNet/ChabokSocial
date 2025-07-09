<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'username'  => $this->username,
            'name'      => $this->name,
            'avatar_url'    => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'is_online' => $this->is_online,
            'last_seen' => $this->last_seen,
        ];
    }
}
