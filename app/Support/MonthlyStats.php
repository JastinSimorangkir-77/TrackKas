<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The monthly totals behind the statistics table on the dashboard.
 *
 * The sums are worked out by the database (one GROUP BY per month), not by
 * loading every transaction into PHP, so the table stays cheap even when an
 * account is full.
 *
 * MySQL and SQLite write "the year and month of a date" in different ways,
 * hence the switch in monthExpression(): the tests run on SQLite while the
 * application itself runs on MySQL.
 */
class MonthlyStats
{
    /**
     * How many months the table shows, the current month included.
     */
    public const MONTHS = 12;

    /**
     * One row per month, newest first. Months without transactions are kept, as
     * zeroes, so the shape of the last year stays visible.
     *
     * @return array<int, object{key: string, label: string, income: float, expense: float, difference: float, sign: string, tone: string, current: bool}>
     */
    public static function forUser(User $user): array
    {
        $first = Carbon::today()->startOfMonth()->subMonths(self::MONTHS - 1);
        $thisMonth = Carbon::today()->format('Y-m');

        $expression = self::monthExpression();

        $totals = $user->transactions()
            ->whereDate('date', '>=', $first->toDateString())
            ->selectRaw("$expression as bulan, type, sum(amount) as total")
            ->groupByRaw("$expression, type")
            ->get()
            ->groupBy('bulan')
            ->map(fn (Collection $group) => [
                'pemasukan' => (float) $group->where('type', 'pemasukan')->sum('total'),
                'pengeluaran' => (float) $group->where('type', 'pengeluaran')->sum('total'),
            ]);

        $months = [];

        for ($i = 0; $i < self::MONTHS; $i++) {
            $month = $first->copy()->addMonths($i);
            $key = $month->format('Y-m');

            $found = $totals->get($key, []);
            $income = $found['pemasukan'] ?? 0.0;
            $expense = $found['pengeluaran'] ?? 0.0;
            $difference = $income - $expense;

            // A month that balances out (or has nothing in it at all) gets no
            // sign and a neutral colour, so "+Rp 0" never shows up.
            $months[] = (object) [
                'key' => $key,
                'label' => ReportPeriod::monthLabel($month),
                'income' => $income,
                'expense' => $expense,
                'difference' => $difference,
                'sign' => $difference > 0 ? '+' : ($difference < 0 ? '-' : ''),
                'tone' => $difference > 0 ? 'positive' : ($difference < 0 ? 'negative' : 'zero'),
                'current' => $key === $thisMonth,
            ];
        }

        // Newest first, the same way the transaction list is ordered.
        return array_reverse($months);
    }

    /**
     * The SQL that turns a date into "2026-10".
     */
    private static function monthExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', \"date\")",
            default => "DATE_FORMAT(`date`, '%Y-%m')",
        };
    }
}
