<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'no_hp' => $this->faker->phoneNumber(),
            'poin' => $this->faker->numberBetween(100, 5000),
        ];
    }
}