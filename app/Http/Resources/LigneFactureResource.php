<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class LigneFactureResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'            => $this->id,
            'designation'   => $this->designation,
            'quantite'      => $this->quantite,
            'prix_unitaire' => round($this->prix_unitaire, 2),
            'taux_tva'      => $this->taux_tva,
            'montant_ht'    => round($this->montant_ht,  2),
            'montant_tva'   => round($this->montant_tva, 2),
        ];
    }
}
