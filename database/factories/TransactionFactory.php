<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['pemasukan', 'pengeluaran']),
            'category' => fake()->randomElement(['Gaji', 'Makanan & Minuman', 'Transportasi', 'Belanja']),
            'amount' => fake()->numberBetween(10, 500) * 1000,
            'date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'note' => fake()->optional()->sentence(3),
        ];
    }
}
