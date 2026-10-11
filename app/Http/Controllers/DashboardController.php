<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Support\MonthlyStats;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard summary of the signed in user.
     */
    public function __invoke(): View
    {
        $income = auth()->user()->transactions()->where('type', 'pemasukan')->sum('amount');
        $expense = auth()->user()->transactions()->where('type', 'pengeluaran')->sum('amount');

        return view('dashboard', [
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'latest' => auth()->user()->transactions()->latest('date')->latest('id')->take(5)->get(),
            'incomeByCategory' => $this->totalsByCategory('pemasukan'),
            'expenseByCategory' => $this->totalsByCategory('pengeluaran'),
            'monthly' => MonthlyStats::forUser(auth()->user()),
            // Shown in the "Tentang TrackKas" section at the bottom of the page.
            'batas' => Transaction::MAX_PER_USER,
        ]);
    }

    /**
     * Total amount per category for the given type.
     */
    private function totalsByCategory(string $type)
    {
        return auth()->user()->transactions()
            ->where('type', $type)
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
    }
}
