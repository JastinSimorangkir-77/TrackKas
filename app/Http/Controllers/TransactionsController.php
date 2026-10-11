<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Support\ReportPeriod;
use App\Support\ReportScope;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    /**
     * How many rows one page of the transaction list holds.
     *
     * An account may keep 2.000 rows and the page used to render all of them at
     * once. Fifty keeps a page light while still showing enough rows to read a
     * month of daily records in one go.
     */
    public const PER_PAGE = 50;

    /**
     * Display a listing of the signed in user's transactions.
     */
    public function index(Request $request)
    {
        // The search box matches the category and the note, both kept together in
        // one group so the type filter still applies on top of it.
        $search = trim((string) $request->query('q', ''));

        // A value that is neither of the two types is ignored instead of being
        // filtered on: a mistyped or hand-edited address then still opens the
        // list, rather than an empty page that looks like a bug.
        $type = $request->query('type');
        $type = in_array($type, ['pemasukan', 'pengeluaran'], true) ? $type : null;

        $transactions = $request->user()->transactions()
            ->when($type, fn ($query, $type) => $query->where('type', $type))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query
                    ->where('category', 'like', '%'.$search.'%')
                    ->orWhere('note', 'like', '%'.$search.'%');
            }))
            ->latest('date')
            ->latest('id')
            ->paginate(self::PER_PAGE)
            // Moving to the next page must not throw the search or the filter away.
            ->withQueryString();

        // The pickers of the report download sit on this page too, so their
        // choices come along: the period as well as the transaction types the
        // report should hold.
        return view('transaksi.index', [
            'transactions' => $transactions,
            'type' => $type,
            'search' => $search,
            'periodeOptions' => ReportPeriod::options(),
            'periode' => ReportPeriod::HARIAN,
            'tanggal' => now()->toDateString(),
            'jenisOptions' => ReportScope::options(),
            'jenis' => ReportScope::SEMUA,
        ]);
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(Request $request)
    {
        return view('transaksi.create', [
            'jumlah' => $request->user()->transactions()->count(),
            'batas' => Transaction::MAX_PER_USER,
        ]);
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $attributes = $request->validate([
            'type' => ['required', 'in:pemasukan,pengeluaran'],
            'date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        // One account may only hold so many rows, otherwise the transaction
        // list and the reports get slow to open.
        if ($user->transactions()->count() >= Transaction::MAX_PER_USER) {
            return back()
                ->withInput()
                ->withErrors(['transaksi' => 'Batas maksimal '.number_format(Transaction::MAX_PER_USER, 0, ',', '.').' transaksi per akun sudah tercapai. Hapus beberapa transaksi dulu sebelum menambah yang baru.']);
        }

        $user->transactions()->create($attributes);

        return redirect()->route('transaksi.index')->with('status', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * Remove the given transaction.
     */
    public function destroy(Request $request, string $id)
    {
        $request->user()->transactions()->findOrFail($id)->delete();

        return redirect()->route('transaksi.index')->with('status', 'Transaksi berhasil dihapus.');
    }
}
