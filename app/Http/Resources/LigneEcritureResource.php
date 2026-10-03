<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class LigneEcritureResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'      => $this->id,
            'compte'  => ['numero' => $this->compte->numero, 'libelle' => $this->compte->libelle],
            'libelle' => $this->libelle,
            'debit'   => round($this->debit,  2),
            'credit'  => round($this->credit, 2),
            'ordre'   => $this->ordre,
        ];
    }
}
