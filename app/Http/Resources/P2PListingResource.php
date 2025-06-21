<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class P2PListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'id_imovel' => $this->id_imovel,
            'qtd_tokens' => $this->qtd_tokens,
            'valor_unitario' => $this->valor_unitario,
            'status' => $this->status,
        ];
    }
}
