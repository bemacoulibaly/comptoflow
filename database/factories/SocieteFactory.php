<?php
namespace Database\Factories;
use App\Models\Societe;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocieteFactory extends Factory
{
    protected $model = Societe::class;
    public function definition(): array
    {
        return [
            'raison_sociale'      => $this->faker->company(),
            'numero_contribuable' => 'CI-ABJ-' . $this->faker->year() . '-' . $this->faker->numerify('#####') . '-X',
            'regime_fiscal'       => $this->faker->randomElement(['reel_simplifie', 'reel_normal']),
            'devise'              => 'XOF',
            'taux_tva'            => 18.00,
            'adresse'             => $this->faker->address(),
            'ville'               => $this->faker->randomElement(['Abidjan', 'Bouaké', 'Yamoussoukro']),
            'telephone'           => '+225 07 ' . $this->faker->numerify('## ## ## ##'),
            'email'               => $this->faker->companyEmail(),
        ];
    }
}
