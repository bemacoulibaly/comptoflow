<?php
namespace Database\Factories;
use App\Models\Ecriture;
use Illuminate\Database\Eloquent\Factories\Factory;

class EcritureFactory extends Factory
{
    protected $model = Ecriture::class;
    public function definition(): array
    {
        $journal = $this->faker->randomElement(['BQ','CA','AC','VT','OD','SA']);
        return [
            'numero_piece'    => $journal . '-' . now()->year . '-' . $this->faker->unique()->numerify('####'),
            'date_ecriture'   => $this->faker->dateThisYear(),
            'journal'         => $journal,
            'libelle'         => $this->faker->sentence(4),
            'reference_tiers' => $this->faker->optional()->company(),
            'statut'          => 'brouillon',
        ];
    }
    public function validee(): static
    {
        return $this->state(fn() => ['statut' => 'validee', 'validee_at' => now()]);
    }
}
