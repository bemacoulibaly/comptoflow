<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class TiersResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'                  => $this->id,
            'type'                => $this->type,
            'nom'                 => $this->nom,
            'email'               => $this->email,
            'telephone'           => $this->telephone,
            'ville'               => $this->ville,
            'numero_contribuable' => $this->numero_contribuable,
            'actif'               => $this->actif,
        ];
    }
}
