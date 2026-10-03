<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class FactureResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'            => $this->id,
            'numero'        => $this->numero,
            'type'          => $this->type,
            'statut'        => $this->statut,
            'date_emission' => $this->date_emission->format('Y-m-d'),
            'date_echeance' => $this->date_echeance->format('Y-m-d'),
            'tiers'         => ['id' => $this->tiers->id, 'nom' => $this->tiers->nom, 'type' => $this->tiers->type],
            'montants'      => [
                'ht'    => round($this->montant_ht,   2),
                'tva'   => round($this->montant_tva,  2),
                'ttc'   => round($this->montant_ttc,  2),
                'paye'  => round($this->montant_paye, 2),
                'solde' => round($this->solde,         2),
            ],
            'notes'      => $this->notes,
            'created_at' => $this->created_at->toIso8601String(),
            'lignes'     => LigneFactureResource::collection($this->whenLoaded('lignes')),
        ];
    }
}
