<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminLoginResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'admin'  => AdminResource::make($this->resource),
            'adminToken' => $this->resource->createToken('authAdminToken')->plainTextToken,
        ];
    }
}
