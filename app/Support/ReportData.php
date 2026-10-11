<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * The numbers behind one report: the rows, the totals and the balance.
 *
 * Both the PDF download and the tests need those numbers, so building them
 * lives here instead of inside the controller. The controller only turns this
 * array into a PDF file.
 */
class ReportData
{
    /**
     * Build the report of one period for one user.
     *
     * The scope decides which transaction types end up in the rows: both, income
     * only, or expense only. Leaving it out reports both, the way the report has
     * always worked.
     *
     * @return array<string, mixed>
     */
    public static function build(User $user, ReportPeriod $period, ?ReportScope $scope = null): array
    {
        $scope ??= ReportScope::make();

        // whereDate, not whereBetween: the database may hold a time next to the
        // date, and then a plain comparison would drop the last day of the range.
        $transactions = $user->transactions()
            ->whereIn('type', $scope->types())
            ->whereDate('date', '>=', $period->startDate())
            ->whereDate('date', '<=', $period->endDate())
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $income = (float) $transactions->where('type', 'pemasukan')->sum(fn ($transaction) => (float) $transaction->amount);
        $expense = (float) $transactions->where('type', 'pengeluaran')->sum(fn ($transaction) => (float) $transaction->amount);

        // The balance the period starts with: everything recorded before it, of
        // both types. The balance describes the account as a whole, so it never
        // changes with the scope and is only printed on the combined report.
        $incomeBefore = (float) $user->transactions()->whereDate('date', '<', $period->startDate())->where('type', 'pemasukan')->sum('amount');
        $expenseBefore = (float) $user->transactions()->whereDate('date', '<', $period->startDate())->where('type', 'pengeluaran')->sum('amount');

        $saldoAwal = $incomeBefore - $expenseBefore;

        // Each row carries the balance after it, so the report can be read
        // without adding the numbers up by hand.
        //
        // A one-sided report gets no balance: with half the rows missing, a
        // running total would silently skip the transactions in between and
        // read as if it were the balance of the account. A null here tells the
        // PDF view to leave that column out.
        $saldo = $saldoAwal;

        $rows = $transactions->map(function ($transaction) use (&$saldo, $scope) {
            $saldo += $transaction->type === 'pemasukan' ? (float) $transaction->amount : -(float) $transaction->amount;

            return (object) [
                'date' => $transaction->date,
                'category' => $transaction->category,
                'note' => $transaction->note,
                'type' => $transaction->type,
                'amount' => (float) $transaction->amount,
                'balance' => $scope->isCombined() ? $saldo : null,
            ];
        });

        return [
            'period' => $period,
            'scope' => $scope,
            'rows' => $rows,
            'jumlah' => $rows->count(),
            'income' => $income,
            'expense' => $expense,
            // A one-sided report shows a single total: the type it kept. The
            // other one is always 0.0 there, but naming it outright keeps the
            // view from having to work out which half it is looking at.
            'scopeTotal' => $scope->isCombined() ? null : ($scope->keeps('pemasukan') ? $income : $expense),
            'saldoAwal' => $saldoAwal,
            'saldoAkhir' => $saldoAwal + $income - $expense,
            'incomeByCategory' => self::totalsByCategory($transactions, 'pemasukan'),
            'expenseByCategory' => self::totalsByCategory($transactions, 'pengeluaran'),
            'user' => $user,
            'generatedAt' => now(),
            'filename' => self::filename($period, $scope),
        ];
    }

    /**
     * "Laporan-Keuangan-Harian-2026-10-07.pdf", with the scope wedged in when the
     * report is one-sided: "Laporan-Keuangan-Pemasukan-Harian-2026-10-07.pdf".
     */
    private static function filename(ReportPeriod $period, ReportScope $scope): string
    {
        $parts = ['Laporan-Keuangan', $scope->filenamePart(), $period->label(), $period->startDate()];

        return implode('-', array_filter($parts)).'.pdf';
    }

    /**
     * Total amount per category, biggest first, for the rows of one type.
     */
    private static function totalsByCategory(Collection $transactions, string $type): Collection
    {
        return $transactions
            ->where('type', $type)
            ->groupBy('category')
            ->map(fn (Collection $group, string $category) => (object) [
                'category' => $category,
                'total' => (float) $group->sum(fn ($transaction) => (float) $transaction->amount),
            ])
            ->sortByDesc('total')
            ->values();
    }
}
