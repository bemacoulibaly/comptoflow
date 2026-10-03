<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFactureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->peutEditer();
    }

    public function rules(): array
    {
        return [
            'tiers_id'              => 'required|exists:tiers,id',
            'type'                  => 'required|in:client,fournisseur',
            'date_emission'         => 'required|date',
            'date_echeance'         => 'required|date|after_or_equal:date_emission',
            'taux_tva'              => 'nullable|numeric|min:0|max:100',
            'notes'                 => 'nullable|string',
            'lignes'                => 'required|array|min:1',
            'lignes.*.designation'  => 'required|string|max:255',
            'lignes.*.quantite'     => 'required|numeric|min:0.01',
            'lignes.*.prix_unitaire'=> 'required|numeric|min:0',
            'lignes.*.taux_tva'     => 'nullable|numeric|min:0|max:100',
            'lignes.*.unite'        => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'lignes.min'                       => 'Une facture doit comporter au moins une ligne.',
            'lignes.*.designation.required'    => 'La désignation est obligatoire pour chaque ligne.',
            'lignes.*.quantite.required'       => 'La quantité est obligatoire.',
            'lignes.*.prix_unitaire.required'  => 'Le prix unitaire est obligatoire.',
            'date_echeance.after_or_equal'     => 'La date d\'échéance doit être postérieure ou égale à la date d\'émission.',
        ];
    }
}
