<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use stdClass;

class TagCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $status     = $this->count() ? true : false;
        $pagination = $status ? new PaginationResource($this) : new stdClass();

        return [
            'status' => $status,
            'message'=> $status ? 'success' : 'failed',
            'data'   => $this->collection,
            'meta'   => $pagination
        ];
    }
}
