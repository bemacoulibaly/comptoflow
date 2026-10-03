<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class EcritureResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'            => $this->id,
            'numero_piece'  => $this->numero_piece,
            'date_ecriture' => $this->date_ecriture->format('Y-m-d'),
            'journal'       => $this->journal,
            'libelle'       => $this->libelle,
            'statut'        => $this->statut,
            'total_debit'   => round($this->total_debit,  2),
            'total_credit'  => round($this->total_credit, 2),
            'equilibree'    => $this->isEquilibree(),
            'validee_at'    => $this->validee_at?->toIso8601String(),
            'created_at'    => $this->created_at->toIso8601String(),
            'lignes'        => LigneEcritureResource::collection($this->whenLoaded('lignes')),
        ];
    }
}
