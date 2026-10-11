<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any frozen clock has to be released again, even when a test fails.
     */
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * The dashboard is only reachable when signed in.
     */
    public function test_guests_cannot_open_authenticated_pages(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('transaksi.index'))->assertRedirect(route('login'));
        $this->get(route('transaksi.create'))->assertRedirect(route('login'));
    }

    /**
     * The dashboard summary is displayed.
     */
    public function test_dashboard_summary_is_displayed(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan keuangan Anda')
            ->assertSee('Total Pemasukan')
            ->assertSee('Total Pengeluaran')
            ->assertSee('Saldo')
            ->assertSee('Belum ada transaksi');
    }

    /**
     * The monthly statistics table sums up the last twelve months.
     */
    public function test_the_monthly_statistics_table_is_displayed(): void
    {
        Carbon::setTestNow('2026-10-07 10:00:00');

        $user = User::factory()->create();

        $user->transactions()->create(['type' => 'pemasukan', 'category' => 'Gaji', 'date' => '2026-10-01', 'amount' => 5000000]);
        $user->transactions()->create(['type' => 'pengeluaran', 'category' => 'Belanja', 'date' => '2026-10-05', 'amount' => 1200000]);

        $response = $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Statistik Bulanan')
            ->assertSee('12 bulan terakhir')
            ->assertSee('Bulan')
            ->assertSee('Pemasukan')
            ->assertSee('Pengeluaran')
            ->assertSee('Selisih')
            // The window runs from November last year to this October.
            ->assertSee('Oktober 2026')
            ->assertSee('November 2025')
            ->assertSee('Bulan ini');

        // The signed difference is what only this table prints.
        $this->assertStringContainsString('+Rp 3.800.000', $response->getContent());

        // Months with nothing in them are not dressed up with a plus sign.
        $this->assertStringNotContainsString('+Rp 0', $response->getContent());
    }

    /**
     * The sidebar user card links to the profile page.
     */
    public function test_sidebar_user_card_links_to_the_profile_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('profile.edit'));
    }

    /**
     * The transaction pages are displayed.
     */
    public function test_transaksi_pages_are_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('transaksi.index'))
            ->assertOk()
            ->assertSee('Riwayat semua transaksi Anda')
            ->assertSee('Daftar Transaksi');

        $this->actingAs($user)
            ->get(route('transaksi.create'))
            ->assertOk()
            ->assertSee('Jenis Transaksi')
            ->assertSee('Simpan Transaksi')
            ->assertSee('maxlength="50"', false)
            ->assertSee('max="999999999999.99"', false);
    }
}
