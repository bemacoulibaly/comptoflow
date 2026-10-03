<?php
namespace Database\Factories;
use App\Models\Tiers;
use Illuminate\Database\Eloquent\Factories\Factory;

class TiersFactory extends Factory
{
    protected $model = Tiers::class;
    public function definition(): array
    {
        return [
            'type'                => $this->faker->randomElement(['client', 'fournisseur']),
            'nom'                 => $this->faker->company(),
            'email'               => $this->faker->companyEmail(),
            'telephone'           => '+225 07 ' . $this->faker->numerify('## ## ## ##'),
            'adresse'             => $this->faker->streetAddress(),
            'ville'               => $this->faker->randomElement(['Abidjan', 'Bouaké', 'Yamoussoukro']),
            'numero_contribuable' => $this->faker->optional()->numerify('CI-###-####-#####-X'),
            'actif'               => true,
        ];
    }
    public function client(): static
    {
        return $this->state(fn() => ['type' => 'client']);
    }
    public function fournisseur(): static
    {
        return $this->state(fn() => ['type' => 'fournisseur']);
    }
}
