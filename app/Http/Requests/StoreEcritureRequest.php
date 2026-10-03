<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEcritureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->peutEditer();
    }

    public function rules(): array
    {
        return [
            'numero_piece'          => 'required|string|max:50',
            'date_ecriture'         => 'required|date',
            'journal'               => 'required|in:BQ,CA,AC,VT,OD,SA',
            'libelle'               => 'required|string|max:255',
            'reference_tiers'       => 'nullable|string|max:191',
            'lignes'                => 'required|array|min:2',
            'lignes.*.numero_compte'=> 'required|string|max:20',
            'lignes.*.libelle'      => 'required|string|max:255',
            'lignes.*.debit'        => 'nullable|numeric|min:0',
            'lignes.*.credit'       => 'nullable|numeric|min:0',
            'lignes.*.tiers_id'     => 'nullable|exists:tiers,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $lignes  = $this->input('lignes', []);
            $debit   = collect($lignes)->sum(fn($l) => floatval($l['debit']  ?? 0));
            $credit  = collect($lignes)->sum(fn($l) => floatval($l['credit'] ?? 0));
            if (abs($debit - $credit) > 0.01) {
                $v->errors()->add('lignes', "L'écriture n'est pas équilibrée : débit {$debit} ≠ crédit {$credit}.");
            }
        });
    }

    public function messages(): array
    {
        return [
            'lignes.min'                    => 'Une écriture doit comporter au moins 2 lignes.',
            'lignes.*.numero_compte.required'=> 'Le numéro de compte est obligatoire pour chaque ligne.',
        ];
    }
}
