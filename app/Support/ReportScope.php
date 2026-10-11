<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Which transaction types one report holds: both, income only, or expense only.
 *
 * This is kept apart from ReportPeriod because the two choices are independent:
 * any period can be reported with any scope. The validation rules and the
 * dropdown both ask this class, so the allowed keys live in exactly one place.
 */
class ReportScope
{
    public const SEMUA = 'semua';

    public const PEMASUKAN = 'pemasukan';

    public const PENGELUARAN = 'pengeluaran';

    private function __construct(
        public readonly string $key,
    ) {}

    /**
     * The scope names the user can pick, ready for a dropdown.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::SEMUA => 'Pemasukan & Pengeluaran',
            self::PEMASUKAN => 'Pemasukan saja',
            self::PENGELUARAN => 'Pengeluaran saja',
        ];
    }

    /**
     * Build the scope from the request value. An empty value means both types,
     * so a report downloaded without choosing anything stays complete.
     */
    public static function make(?string $key = null): self
    {
        $key = $key === null || $key === '' ? self::SEMUA : $key;

        if (! array_key_exists($key, self::options())) {
            throw new InvalidArgumentException("Cakupan laporan tidak dikenal: {$key}");
        }

        return new self($key);
    }

    /**
     * "Pemasukan & Pengeluaran", "Pemasukan saja" or "Pengeluaran saja".
     */
    public function label(): string
    {
        return self::options()[$this->key];
    }

    /**
     * True when both types are reported side by side.
     *
     * Only then does a running balance make sense: an expense report that never
     * shows the income it spends has no single balance to walk through, and a
     * partial total must not be mistaken for the balance of the account.
     */
    public function isCombined(): bool
    {
        return $this->key === self::SEMUA;
    }

    /**
     * Whether a transaction of this type belongs in the report.
     */
    public function keeps(string $type): bool
    {
        return in_array($type, $this->types(), true);
    }

    /**
     * The single total a one-sided report shows, or null when both types are
     * reported and the usual income, expense and balance rows are printed instead.
     */
    public function totalLabel(): ?string
    {
        return match ($this->key) {
            self::PEMASUKAN => 'Total pemasukan',
            self::PENGELUARAN => 'Total pengeluaran',
            default => null,
        };
    }

    /**
     * The transaction types this scope keeps.
     *
     * @return array<int, string>
     */
    public function types(): array
    {
        return match ($this->key) {
            self::PEMASUKAN => ['pemasukan'],
            self::PENGELUARAN => ['pengeluaran'],
            default => ['pemasukan', 'pengeluaran'],
        };
    }

    /**
     * The scope as it appears in the file name, empty for the combined report so
     * that report keeps the file name it has always had.
     */
    public function filenamePart(): string
    {
        return match ($this->key) {
            self::PEMASUKAN => 'Pemasukan',
            self::PENGELUARAN => 'Pengeluaran',
            default => '',
        };
    }
}
