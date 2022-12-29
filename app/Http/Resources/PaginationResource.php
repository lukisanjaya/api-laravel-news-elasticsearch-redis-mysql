<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
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
            "current_page"  => (int) $this->currentPage(),
            "last_page"     => (int) $this->lastPage(),
            "prev_page_url" => (string) $this->previousPageUrl(),
            "next_page_url" => (string) $this->nextPageUrl(),
            "per_page"      => (int) $this->perPage(),
            "from"          => (int) $this->firstItem(),
            "to"            => (int) $this->lastItem(),
            "total"         => (int) $this->total(),
            // "path"          => url()
        ];
    }
}
