<?php

namespace Database\Factories\Member;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('(##) #####-####'),
            'birth_date' => $this->faker->date('Y-m-d', '-18 years'),
            'cpf' => $this->faker->regexify('[0-9]{11}'),
            'city' => $this->faker->city(),
            'neighborhood' => $this->faker->streetName(),
            'street' => $this->faker->streetAddress(),
            'number' => $this->faker->buildingNumber(),
        ];
    }
}
