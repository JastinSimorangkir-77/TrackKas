<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Support\ReportData;
use App\Support\ReportPeriod;
use App\Support\ReportScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_download_a_report(): void
    {
        $this->get(route('laporan.pdf'))->assertRedirect(route('login'));
    }

    public function test_the_report_has_no_page_of_its_own(): void
    {
        // The report moved onto the transaction page, so the old address is gone.
        $this->assertFalse(Route::has('laporan.index'));

        $this->actingAs(User::factory()->create())
            ->get('/laporan')
            ->assertNotFound();
    }

    public function test_the_transaction_page_offers_the_report_download(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('transaksi.index'))
            ->assertOk()
            ->assertSee('name="periode"', false)
            ->assertSee('name="tanggal"', false)
            ->assertSee('Unduh PDF')
            ->assertSee(route('laporan.pdf'));

        // The report itself (its rows and totals) lives in the PDF only.
        $response->assertDontSee('Rincian Transaksi');
        $response->assertDontSee('Saldo awal periode');
    }

    public function test_the_daily_report_covers_only_that_day_and_the_users_own_transactions(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['date' => '2026-10-07', 'category' => 'Sarapan', 'note' => null]);
        Transaction::factory()->for($user)->create(['date' => '2026-10-06', 'category' => 'HariLain', 'note' => null]);
        Transaction::factory()->create(['date' => '2026-10-07', 'category' => 'MilikOrangLain', 'note' => null]);

        $period = ReportPeriod::make(ReportPeriod::HARIAN, '2026-10-07');
        $report = ReportData::build($user, $period);

        $this->assertSame('7 Oktober 2026', $period->rangeLabel());
        $this->assertSame(['Sarapan'], $report['rows']->pluck('category')->all());

        // The PDF view is fed by the same report, so the day's row reaches it.
        $this->assertStringContainsString('Sarapan', view('laporan.pdf', $report)->render());
    }

    public function test_the_weekly_report_covers_monday_until_sunday(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['date' => '2026-10-05', 'category' => 'SeninAwal', 'note' => null]);
        Transaction::factory()->for($user)->create(['date' => '2026-10-11', 'category' => 'MingguAkhir', 'note' => null]);
        Transaction::factory()->for($user)->create(['date' => '2026-10-04', 'category' => 'SebelumPekan', 'note' => null]);
        Transaction::factory()->for($user)->create(['date' => '2026-10-12', 'category' => 'SetelahPekan', 'note' => null]);

        $period = ReportPeriod::make(ReportPeriod::MINGGUAN, '2026-10-07');
        $report = ReportData::build($user, $period);

        $this->assertSame('5 - 11 Oktober 2026', $period->rangeLabel());
        $this->assertSame(['SeninAwal', 'MingguAkhir'], $report['rows']->pluck('category')->all());
    }

    public function test_the_monthly_report_covers_the_whole_month(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['date' => '2026-10-01', 'category' => 'AwalBulan', 'note' => null]);
        Transaction::factory()->for($user)->create(['date' => '2026-10-31', 'category' => 'AkhirBulan', 'note' => null]);
        Transaction::factory()->for($user)->create(['date' => '2026-09-30', 'category' => 'BulanLalu', 'note' => null]);
        Transaction::factory()->for($user)->create(['date' => '2026-11-01', 'category' => 'BulanDepan', 'note' => null]);

        $period = ReportPeriod::make(ReportPeriod::BULANAN, '2026-10-20');
        $report = ReportData::build($user, $period);

        $this->assertSame('Oktober 2026', $period->rangeLabel());
        $this->assertSame(['AwalBulan', 'AkhirBulan'], $report['rows']->pluck('category')->all());
    }

    public function test_the_yearly_report_covers_the_whole_year(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['date' => '2026-01-01', 'category' => 'AwalTahun', 'note' => null]);
        Transaction::factory()->for($user)->create(['date' => '2026-12-31', 'category' => 'AkhirTahun', 'note' => null]);
        Transaction::factory()->for($user)->create(['date' => '2025-12-31', 'category' => 'TahunLalu', 'note' => null]);

        $period = ReportPeriod::make(ReportPeriod::TAHUNAN, '2026-06-15');
        $report = ReportData::build($user, $period);

        $this->assertSame('2026', $period->rangeLabel());
        $this->assertSame(['AwalTahun', 'AkhirTahun'], $report['rows']->pluck('category')->all());
    }

    public function test_the_report_uses_the_balance_before_the_period_as_its_starting_point(): void
    {
        $user = User::factory()->create();

        // 500.000 - 200.000 recorded in September, so October starts with 300.000.
        Transaction::factory()->for($user)->create(['type' => 'pemasukan', 'date' => '2026-09-05', 'amount' => 500000]);
        Transaction::factory()->for($user)->create(['type' => 'pengeluaran', 'date' => '2026-09-20', 'amount' => 200000]);

        Transaction::factory()->for($user)->create(['type' => 'pemasukan', 'date' => '2026-10-10', 'amount' => 100000]);

        $report = ReportData::build($user, ReportPeriod::make(ReportPeriod::BULANAN, '2026-10-10'));

        $this->assertSame(300000.0, $report['saldoAwal']);
        $this->assertSame(100000.0, $report['income']);
        $this->assertSame(400000.0, $report['saldoAkhir']);
    }

    public function test_a_period_without_transactions_has_nothing_to_report(): void
    {
        $user = User::factory()->create();

        $report = ReportData::build($user, ReportPeriod::make(ReportPeriod::HARIAN, '2026-10-07'));

        $this->assertSame(0, $report['jumlah']);
        $this->assertTrue($report['rows']->isEmpty());
    }

    public function test_the_pdf_report_is_downloaded_as_a_pdf_file(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['date' => '2026-10-07', 'category' => 'Sarapan']);

        $response = $this->actingAs($user)->get(route('laporan.pdf', ['periode' => 'harian', 'tanggal' => '2026-10-07']));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        $this->assertStringContainsString(
            'Laporan-Keuangan-Harian-2026-10-07.pdf',
            $response->headers->get('content-disposition'),
        );

        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_the_pdf_of_a_period_without_transactions_is_still_downloaded(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('laporan.pdf', ['periode' => 'bulanan', 'tanggal' => '2026-10-10']));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_an_unknown_period_or_date_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('laporan.pdf', ['periode' => 'per-jam', 'tanggal' => 'bukan-tanggal']))
            ->assertSessionHasErrors(['periode', 'tanggal']);
    }

    public function test_the_transaction_page_offers_the_report_scope_choice(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('transaksi.index'))
            ->assertOk()
            ->assertSee('name="jenis"', false)
            ->assertSee('Pemasukan & Pengeluaran')
            ->assertSee('Pemasukan saja')
            ->assertSee('Pengeluaran saja');
    }

    public function test_the_report_holds_both_types_when_no_scope_is_chosen(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['type' => 'pemasukan', 'date' => '2026-10-05', 'category' => 'Gaji', 'amount' => 5000000]);
        Transaction::factory()->for($user)->create(['type' => 'pengeluaran', 'date' => '2026-10-06', 'category' => 'Belanja', 'amount' => 1200000]);

        $report = ReportData::build($user, ReportPeriod::make(ReportPeriod::BULANAN, '2026-10-10'));

        $this->assertTrue($report['scope']->isCombined());
        $this->assertSame(['Gaji', 'Belanja'], $report['rows']->pluck('category')->all());
        $this->assertSame(3800000.0, $report['saldoAkhir']);
        $this->assertNotNull($report['rows']->first()->balance);
    }

    public function test_the_income_only_report_leaves_the_expenses_out(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['type' => 'pemasukan', 'date' => '2026-10-05', 'category' => 'Gaji', 'amount' => 5000000]);
        Transaction::factory()->for($user)->create(['type' => 'pengeluaran', 'date' => '2026-10-06', 'category' => 'Belanja', 'amount' => 1200000]);

        $report = ReportData::build(
            $user,
            ReportPeriod::make(ReportPeriod::BULANAN, '2026-10-10'),
            ReportScope::make(ReportScope::PEMASUKAN),
        );

        $this->assertSame(['Gaji'], $report['rows']->pluck('category')->all());
        $this->assertSame(1, $report['jumlah']);
        $this->assertSame(5000000.0, $report['income']);
        $this->assertSame(0.0, $report['expense']);
        $this->assertSame(5000000.0, $report['scopeTotal']);
        $this->assertTrue($report['expenseByCategory']->isEmpty());

        // With half the rows missing, a running balance would read as the balance
        // of the account, so a one-sided report carries none.
        $this->assertNull($report['rows']->first()->balance);

        $html = view('laporan.pdf', $report)->render();

        $this->assertStringContainsString('Gaji', $html);
        $this->assertStringContainsString('Pemasukan saja', $html);
        $this->assertStringContainsString('Total pemasukan', $html);
        $this->assertStringNotContainsString('Belanja', $html);
        $this->assertStringNotContainsString('Pengeluaran', $html);
        $this->assertStringNotContainsString('Saldo', $html);
    }

    public function test_the_expense_only_report_leaves_the_income_out(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['type' => 'pemasukan', 'date' => '2026-10-05', 'category' => 'Gaji', 'amount' => 5000000]);
        Transaction::factory()->for($user)->create(['type' => 'pengeluaran', 'date' => '2026-10-06', 'category' => 'Belanja', 'amount' => 1200000]);

        $report = ReportData::build(
            $user,
            ReportPeriod::make(ReportPeriod::BULANAN, '2026-10-10'),
            ReportScope::make(ReportScope::PENGELUARAN),
        );

        $this->assertSame(['Belanja'], $report['rows']->pluck('category')->all());
        $this->assertSame(0.0, $report['income']);
        $this->assertSame(1200000.0, $report['expense']);
        $this->assertSame(1200000.0, $report['scopeTotal']);
        $this->assertNull($report['rows']->first()->balance);

        $html = view('laporan.pdf', $report)->render();

        $this->assertStringContainsString('Belanja', $html);
        $this->assertStringContainsString('Pengeluaran saja', $html);
        $this->assertStringContainsString('Total pengeluaran', $html);
        $this->assertStringNotContainsString('Gaji', $html);
        $this->assertStringNotContainsString('Pemasukan', $html);
    }

    public function test_the_balances_still_describe_the_whole_account_on_a_one_sided_report(): void
    {
        $user = User::factory()->create();

        // 500.000 - 200.000 in September, so October starts with 300.000.
        Transaction::factory()->for($user)->create(['type' => 'pemasukan', 'date' => '2026-09-05', 'category' => 'GajiLalu', 'amount' => 500000]);
        Transaction::factory()->for($user)->create(['type' => 'pengeluaran', 'date' => '2026-09-20', 'category' => 'BelanjaLalu', 'amount' => 200000]);

        $report = ReportData::build(
            $user,
            ReportPeriod::make(ReportPeriod::BULANAN, '2026-10-10'),
            ReportScope::make(ReportScope::PEMASUKAN),
        );

        $this->assertSame(300000.0, $report['saldoAwal']);
        $this->assertSame(300000.0, $report['saldoAkhir']);
        $this->assertTrue($report['rows']->isEmpty());

        // A one-sided report with nothing in it still renders, list and all.
        $html = view('laporan.pdf', $report)->render();

        $this->assertStringContainsString('Tidak ada transaksi pada periode ini.', $html);
        $this->assertStringContainsString('Total pemasukan', $html);
        $this->assertStringNotContainsString('Saldo', $html);
    }

    public function test_the_downloaded_report_file_names_the_chosen_scope(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['type' => 'pengeluaran', 'date' => '2026-10-07', 'category' => 'Belanja']);

        $response = $this->actingAs($user)->get(route('laporan.pdf', [
            'periode' => 'harian',
            'tanggal' => '2026-10-07',
            'jenis' => 'pengeluaran',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        $this->assertStringContainsString(
            'Laporan-Keuangan-Pengeluaran-Harian-2026-10-07.pdf',
            $response->headers->get('content-disposition'),
        );

        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_the_combined_report_is_downloaded_when_no_scope_is_sent(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('laporan.pdf', ['periode' => 'harian', 'tanggal' => '2026-10-07']))
            ->assertOk()
            ->assertDownload('Laporan-Keuangan-Harian-2026-10-07.pdf');
    }

    public function test_an_unknown_scope_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('laporan.pdf', ['jenis' => 'tabungan']))
            ->assertSessionHasErrors(['jenis']);
    }
}
