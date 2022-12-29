<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $status = $this->count() ? true : false;

        return [
            'status' => $status,
            'message'=> $status ? 'success' : 'failed',
            'data'   => parent::toArray($request)
        ];
    }
}
