<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_list_shows_only_the_users_own_transactions(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['category' => 'Gaji']);
        Transaction::factory()->create(['category' => 'Belanja']);

        $this->actingAs($user)
            ->get(route('transaksi.index'))
            ->assertOk()
            ->assertSee('Gaji')
            ->assertDontSee('Belanja');
    }

    public function test_a_transaction_can_be_stored(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('transaksi.store'), [
            'type' => 'pengeluaran',
            'date' => '2026-09-20',
            'category' => 'Transportasi',
            'amount' => 25000,
            'note' => 'Ojek online',
        ]);

        $response->assertRedirect(route('transaksi.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'pengeluaran',
            'category' => 'Transportasi',
            'note' => 'Ojek online',
        ]);
    }

    public function test_a_transaction_needs_valid_data(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('transaksi.store'), [
                'type' => 'bukan-jenis',
                'date' => '',
                'category' => '',
                'amount' => '',
            ])
            ->assertSessionHasErrors(['type', 'date', 'category', 'amount']);
    }

    public function test_a_category_longer_than_fifty_characters_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('transaksi.store'), [
                'type' => 'pengeluaran',
                'date' => '2026-09-20',
                'category' => str_repeat('a', 51),
                'amount' => 10000,
            ])
            ->assertSessionHasErrors('category');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_an_amount_above_the_maximum_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('transaksi.store'), [
                'type' => 'pemasukan',
                'date' => '2026-09-20',
                'category' => 'Gaji',
                'amount' => 1000000000000,
            ])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_the_limits_themselves_are_still_accepted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('transaksi.store'), [
                'type' => 'pemasukan',
                'date' => '2026-09-20',
                'category' => str_repeat('a', 50),
                'amount' => 999999999999.99,
            ])
            ->assertRedirect(route('transaksi.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category' => str_repeat('a', 50),
        ]);
    }

    public function test_the_list_is_paginated_fifty_rows_at_a_time(): void
    {
        $user = User::factory()->create();

        // 55 rows, oldest first, so the newest one is the last created.
        for ($i = 1; $i <= 55; $i++) {
            Transaction::factory()->for($user)->create([
                'category' => 'Baris '.$i,
                'date' => now()->subDays(55 - $i)->toDateString(),
            ]);
        }

        $first = $this->actingAs($user)->get(route('transaksi.index'))->assertOk();
        $page = $first->viewData('transactions');

        $this->assertSame(50, $page->count());
        $this->assertSame(55, $page->total());
        $this->assertSame(2, $page->lastPage());

        // Newest first: page one stops at 'Baris 6', the fifth row is on page two.
        $this->assertSame('Baris 55', $page->first()->category);
        $this->assertSame('Baris 6', $page->last()->category);

        $second = $this->actingAs($user)->get(route('transaksi.index', ['page' => 2]))->assertOk();

        $this->assertSame(5, $second->viewData('transactions')->count());
        $this->assertSame('Baris 5', $second->viewData('transactions')->first()->category);
    }

    public function test_the_list_can_be_searched_by_category_and_note(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['category' => 'Kopi Kenangan', 'note' => null]);
        Transaction::factory()->for($user)->create(['category' => 'Transportasi', 'note' => 'Naik ojek ke kampus']);
        Transaction::factory()->for($user)->create(['category' => 'Belanja', 'note' => null]);

        // The category is matched without regard to capital letters.
        $byCategory = $this->actingAs($user)->get(route('transaksi.index', ['q' => 'kopi']))->assertOk();
        $this->assertSame(['Kopi Kenangan'], $byCategory->viewData('transactions')->pluck('category')->all());

        // The note is searched too, not only the category.
        $byNote = $this->actingAs($user)->get(route('transaksi.index', ['q' => 'kampus']))->assertOk();
        $this->assertSame(['Transportasi'], $byNote->viewData('transactions')->pluck('category')->all());
    }

    public function test_the_search_can_be_combined_with_the_type_filter(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['type' => 'pemasukan', 'category' => 'Gaji Bulanan']);
        Transaction::factory()->for($user)->create(['type' => 'pengeluaran', 'category' => 'Gaji Karyawan']);

        $income = $this->actingAs($user)
            ->get(route('transaksi.index', ['q' => 'gaji', 'type' => 'pemasukan']))
            ->assertOk();

        $this->assertSame(['Gaji Bulanan'], $income->viewData('transactions')->pluck('category')->all());

        $expense = $this->actingAs($user)
            ->get(route('transaksi.index', ['q' => 'gaji', 'type' => 'pengeluaran']))
            ->assertOk();

        $this->assertSame(['Gaji Karyawan'], $expense->viewData('transactions')->pluck('category')->all());
    }

    public function test_the_filter_links_keep_the_search_term(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('transaksi.index', ['q' => 'gaji']))
            ->assertOk()
            // Both keys, in the order the view writes them.
            ->assertSee(route('transaksi.index', ['type' => 'pemasukan', 'q' => 'gaji']))
            ->assertSee(route('transaksi.index', ['type' => 'pengeluaran', 'q' => 'gaji']));
    }

    public function test_moving_to_the_next_page_keeps_the_search_and_the_filter(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['type' => 'pemasukan', 'category' => 'Gaji Bulanan']);
        Transaction::factory()->for($user)->create(['type' => 'pengeluaran', 'category' => 'Gaji Lainnya', 'note' => 'gaji']);

        $response = $this->actingAs($user)
            ->get(route('transaksi.index', ['q' => 'gaji', 'type' => 'pemasukan']))
            ->assertOk();

        // Only one match, so there is nothing to page through; the search box and
        // the filter still carry their values into the form and the filter links.
        $response->assertSee('value="gaji"', false)
            ->assertSee('name="type" value="pemasukan"', false);

        // With more rows than fit on a page, the page numbers keep both along.
        Transaction::factory()->for($user)->count(51)->create(['type' => 'pemasukan', 'category' => 'Gaji Bulanan']);

        $paged = $this->actingAs($user)
            ->get(route('transaksi.index', ['q' => 'gaji', 'type' => 'pemasukan']))
            ->assertOk();

        $next = $paged->viewData('transactions')->nextPageUrl();

        $this->assertStringContainsString('q=gaji', $next);
        $this->assertStringContainsString('type=pemasukan', $next);
        $this->assertStringContainsString('page=2', $next);
    }

    public function test_a_search_without_matches_says_so_instead_of_claiming_an_empty_account(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['category' => 'Gaji']);

        $this->actingAs($user)
            ->get(route('transaksi.index', ['q' => 'zzz-tidak-ada']))
            ->assertOk()
            ->assertSee('Tidak ada transaksi yang cocok')
            ->assertSee('Lihat semua transaksi')
            ->assertDontSee('Belum ada transaksi');
    }

    public function test_an_unknown_type_filter_is_ignored(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['category' => 'Gaji']);

        $this->actingAs($user)
            ->get(route('transaksi.index', ['type' => 'entah-apa']))
            ->assertOk()
            ->assertSee('Gaji');
    }

    public function test_a_transaction_is_still_accepted_just_below_the_limit(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->count(Transaction::MAX_PER_USER - 1)->create();

        $this->actingAs($user)
            ->post(route('transaksi.store'), [
                'type' => 'pengeluaran',
                'date' => '2026-09-20',
                'category' => 'Transportasi',
                'amount' => 25000,
            ])
            ->assertRedirect(route('transaksi.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('transactions', Transaction::MAX_PER_USER);
    }

    public function test_a_transaction_is_rejected_once_the_account_is_full(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->count(Transaction::MAX_PER_USER)->create();

        $this->actingAs($user)
            ->post(route('transaksi.store'), [
                'type' => 'pengeluaran',
                'date' => '2026-09-20',
                'category' => 'Transportasi',
                'amount' => 25000,
            ])
            ->assertSessionHasErrors('transaksi');

        $this->assertDatabaseCount('transactions', Transaction::MAX_PER_USER);

        // The ceiling belongs to the account, not to the whole application.
        $this->actingAs(User::factory()->create())
            ->post(route('transaksi.store'), [
                'type' => 'pengeluaran',
                'date' => '2026-09-20',
                'category' => 'Transportasi',
                'amount' => 25000,
            ])
            ->assertRedirect(route('transaksi.index'));
    }

    public function test_the_create_page_hides_the_form_once_the_account_is_full(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->count(Transaction::MAX_PER_USER)->create();

        $this->actingAs($user)
            ->get(route('transaksi.create'))
            ->assertOk()
            ->assertSee('Batas transaksi sudah tercapai')
            ->assertDontSee('Simpan Transaksi');
    }

    public function test_a_transaction_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('transaksi.destroy', $transaction))
            ->assertRedirect(route('transaksi.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    public function test_a_user_cannot_delete_another_users_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->delete(route('transaksi.destroy', $transaction))
            ->assertNotFound();

        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    }

    public function test_the_dashboard_totals_up_the_transactions(): void
    {
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create(['type' => 'pemasukan', 'amount' => 500000]);
        Transaction::factory()->for($user)->create(['type' => 'pengeluaran', 'amount' => 200000]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp 500.000')
            ->assertSee('Rp 200.000')
            ->assertSee('Rp 300.000');
    }

    public function test_the_dashboard_explains_what_trackkas_is(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tentang TrackKas')
            ->assertSee('pencatat keuangan pribadi')
            ->assertSee('diunduh sebagai berkas PDF dari halaman Transaksi')
            ->assertSee('2.000 transaksi');

        // The explanation sits in a card like the rest of the dashboard, not
        // as a bare block of text on the page background.
        $this->assertStringContainsString(
            '<section class="mt-6 rounded-xl border border-gray-100 bg-white shadow-sm">',
            $response->getContent(),
        );
    }

    public function test_the_add_transaction_button_appears_once_on_the_transaction_page(): void
    {
        // A filled list only carries the button in the list header.
        $user = User::factory()->create();

        Transaction::factory()->for($user)->create();

        $filled = $this->actingAs($user)->get(route('transaksi.index'))->assertOk()->getContent();

        $this->assertSame(1, substr_count($filled, route('transaksi.create')));

        // The empty state used to offer a second button in the middle of the page.
        $empty = $this->actingAs(User::factory()->create())
            ->get(route('transaksi.index'))
            ->assertOk()
            ->getContent();

        $this->assertSame(1, substr_count($empty, route('transaksi.create')));
        $this->assertStringContainsString('Gunakan tombol Tambah Transaksi di atas', $empty);
    }
}
