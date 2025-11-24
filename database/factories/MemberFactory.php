<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Member;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        do {
            $randomId = rand(100, 999);
        } while (Member::where('id_member', $randomId)->exists());

        $poin = $this->faker->numberBetween(0, 500);
        $tipeLangganan = Member::getTierByPoin($poin);

        return [
            'id_member' => (string) $randomId,
            'nama' => $this->faker->name(),
            'no_hp' => $this->faker->phoneNumber(),
            'poin' => $poin,
            'tipe_langganan' => $tipeLangganan,
        ];
    }
}