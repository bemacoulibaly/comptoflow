<?php
namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;
    public function definition(): array
    {
        return [
            'nom'        => $this->faker->lastName(),
            'prenom'     => $this->faker->firstName(),
            'email'      => $this->faker->unique()->safeEmail(),
            'password'   => Hash::make('password'),
            'role'       => 'editeur',
            'societe_id' => null,
        ];
    }
    public function admin(): static
    {
        return $this->state(fn() => ['role' => 'admin']);
    }
    public function lecteur(): static
    {
        return $this->state(fn() => ['role' => 'lecteur']);
    }
}
