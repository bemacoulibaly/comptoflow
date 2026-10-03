<?php
namespace Database\Factories;
use App\Models\Compte;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompteFactory extends Factory
{
    protected $model = Compte::class;
    public function definition(): array
    {
        $classe = (string) $this->faker->numberBetween(1, 7);
        $type = match($classe) {
            '6'     => 'charge',
            '7'     => 'produit',
            '1','2' => 'passif',
            default => $this->faker->randomElement(['actif', 'passif']),
        };
        return [
            'numero'  => $this->faker->unique()->numerify('###'),
            'libelle' => $this->faker->words(3, true),
            'classe'  => $classe,
            'type'    => $type,
            'actif'   => true,
        ];
    }
}
