<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Support\MonthlyStats;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MonthlyStatsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The window always ends with the current month, so the clock is frozen
        // to keep the expected labels stable.
        Carbon::setTestNow('2026-10-07 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_the_table_covers_twelve_months_ending_with_the_current_one(): void
    {
        $months = MonthlyStats::forUser(User::factory()->create());

        $this->assertCount(12, $months);

        // Newest first, the same way the transaction list is ordered.
        $this->assertSame('2026-10', $months[0]->key);
        $this->assertSame('Oktober 2026', $months[0]->label);
        $this->assertTrue($months[0]->current);

        $this->assertSame('2025-11', $months[11]->key);
        $this->assertSame('November 2025', $months[11]->label);
        $this->assertFalse($months[11]->current);
    }

    public function test_each_month_gets_its_own_totals_and_difference(): void
    {
        $user = User::factory()->create();

        $user->transactions()->create(['type' => 'pemasukan', 'category' => 'Gaji', 'date' => '2026-10-01', 'amount' => 5000000]);
        $user->transactions()->create(['type' => 'pengeluaran', 'category' => 'Belanja', 'date' => '2026-10-05', 'amount' => 1200000]);
        $user->transactions()->create(['type' => 'pengeluaran', 'category' => 'Transportasi', 'date' => '2026-09-20', 'amount' => 150000]);

        $months = collect(MonthlyStats::forUser($user))->keyBy('key');

        $this->assertSame(5000000.0, $months['2026-10']->income);
        $this->assertSame(1200000.0, $months['2026-10']->expense);
        $this->assertSame(3800000.0, $months['2026-10']->difference);
        $this->assertSame('+', $months['2026-10']->sign);
        $this->assertSame('positive', $months['2026-10']->tone);

        // September only had an expense, so its difference is negative.
        $this->assertSame(0.0, $months['2026-09']->income);
        $this->assertSame(150000.0, $months['2026-09']->expense);
        $this->assertSame(-150000.0, $months['2026-09']->difference);
        $this->assertSame('-', $months['2026-09']->sign);
        $this->assertSame('negative', $months['2026-09']->tone);
    }

    public function test_months_without_transactions_are_kept_as_zero(): void
    {
        $months = MonthlyStats::forUser(User::factory()->create());

        foreach ($months as $month) {
            $this->assertSame(0.0, $month->income);
            $this->assertSame(0.0, $month->expense);
            $this->assertSame(0.0, $month->difference);

            // No "+Rp 0" and no green zero either.
            $this->assertSame('', $month->sign);
            $this->assertSame('zero', $month->tone);
        }
    }

    public function test_only_the_users_own_transactions_are_counted(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->create(['type' => 'pemasukan', 'date' => '2026-10-02', 'amount' => 900000]);

        $months = collect(MonthlyStats::forUser($user))->keyBy('key');

        $this->assertSame(0.0, $months['2026-10']->income);
    }

    public function test_transactions_before_the_window_are_left_out(): void
    {
        $user = User::factory()->create();

        // The window starts in November 2025, so this one is a month too early.
        $user->transactions()->create(['type' => 'pemasukan', 'category' => 'Lama', 'date' => '2025-10-31', 'amount' => 1000000]);

        // This one sits exactly on the first month still inside the window.
        $user->transactions()->create(['type' => 'pemasukan', 'category' => 'Batas', 'date' => '2025-11-01', 'amount' => 250000]);

        $months = collect(MonthlyStats::forUser($user))->keyBy('key');

        $this->assertSame(250000.0, $months['2025-11']->income);
        $this->assertArrayNotHasKey('2025-10', $months->all());
    }

    public function test_a_row_with_a_time_of_day_still_lands_in_its_month(): void
    {
        $user = User::factory()->create();

        // Written straight to the table: the date column can hold a time of day,
        // and that must not push the row out of its month.
        DB::table('transactions')->insert([
            'user_id' => $user->id,
            'type' => 'pengeluaran',
            'category' => 'Malam',
            'amount' => 40000,
            'date' => '2026-10-31 23:30:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $months = collect(MonthlyStats::forUser($user))->keyBy('key');

        $this->assertSame(40000.0, $months['2026-10']->expense);
    }
}
