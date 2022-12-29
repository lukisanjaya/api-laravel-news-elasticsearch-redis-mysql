<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"                => (int) $this->id,
            "name"              => (string) $this->name,
            "email"             => (string) $this->email,
            "username"          => (string) $this->username,
            "avatar"            => (string) $this->avatar,
            "address"           => (string) $this->address,
            "roles"             => (string) $this->roles,
            "email_verified_at" => (string) $this->email_verified_at,
            "created_at"        => (string) $this->created_at,
            "updated_at"        => (string) $this->updated_at
        ];
    }
}
