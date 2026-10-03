<?php
namespace Database\Factories;
use App\Models\LigneEcriture;
use Illuminate\Database\Eloquent\Factories\Factory;

class LigneEcritureFactory extends Factory
{
    protected $model = LigneEcriture::class;
    public function definition(): array
    {
        $montant = $this->faker->numberBetween(10000, 5000000);
        $isDebit = $this->faker->boolean();
        return [
            'libelle' => $this->faker->words(3, true),
            'debit'   => $isDebit ? $montant : 0,
            'credit'  => $isDebit ? 0 : $montant,
            'ordre'   => $this->faker->numberBetween(0, 10),
        ];
    }
}
