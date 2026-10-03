<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class CompteResource extends JsonResource {
    public function toArray(Request $request): array {
        return ['id'=>$this->id,'numero'=>$this->numero,'libelle'=>$this->libelle,'classe'=>$this->classe,'type'=>$this->type,'actif'=>$this->actif];
    }
}
