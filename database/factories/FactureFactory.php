<?php
namespace Database\Factories;
use App\Models\Facture;
use Illuminate\Database\Eloquent\Factories\Factory;

class FactureFactory extends Factory
{
    protected $model = Facture::class;
    public function definition(): array
    {
        $type    = $this->faker->randomElement(['client', 'fournisseur']);
        $prefix  = $type === 'client' ? 'FAC' : 'FF';
        $ht      = $this->faker->numberBetween(50000, 5000000);
        $tva     = round($ht * 0.18, 2);
        $emission = $this->faker->dateThisYear();
        return [
            'numero'         => $prefix . '-' . now()->year . '-' . $this->faker->unique()->numerify('####'),
            'type'           => $type,
            'date_emission'  => $emission,
            'date_echeance'  => $this->faker->dateTimeBetween($emission, '+60 days')->format('Y-m-d'),
            'montant_ht'     => $ht,
            'taux_tva'       => 18.00,
            'montant_tva'    => $tva,
            'montant_ttc'    => $ht + $tva,
            'montant_paye'   => 0,
            'statut'         => 'emise',
        ];
    }
    public function payee(): static
    {
        return $this->state(fn(array $a) => [
            'statut'       => 'payee',
            'montant_paye' => $a['montant_ttc'],
        ]);
    }
    public function enRetard(): static
    {
        return $this->state(fn() => [
            'statut'       => 'en_retard',
            'date_echeance'=> now()->subDays(10)->toDateString(),
        ]);
    }
}
