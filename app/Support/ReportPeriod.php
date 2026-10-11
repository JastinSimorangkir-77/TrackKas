<?php

namespace App\Support;

use Carbon\Carbon;
use InvalidArgumentException;

/**
 * The date range behind one report: harian, mingguan, bulanan or tahunan.
 *
 * The report page and the PDF download both need the same range and the same
 * label, so the maths lives here once instead of being repeated in the
 * controller. Month names are written out instead of using the locale helper,
 * so the labels are the same no matter which locale the app runs with.
 */
class ReportPeriod
{
    public const HARIAN = 'harian';

    public const MINGGUAN = 'mingguan';

    public const BULANAN = 'bulanan';

    public const TAHUNAN = 'tahunan';

    /**
     * Indonesian month names, indexed by Carbon's month number (1-12).
     *
     * @var array<int, string>
     */
    private const BULAN = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    private function __construct(
        public readonly string $key,
        public readonly Carbon $start,
        public readonly Carbon $end,
    ) {}

    /**
     * The period names the user can pick, ready for a dropdown.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::HARIAN => 'Harian',
            self::MINGGUAN => 'Mingguan',
            self::BULANAN => 'Bulanan',
            self::TAHUNAN => 'Tahunan',
        ];
    }

    /**
     * Build the period that contains the anchor date (today when it is null).
     */
    public static function make(string $key, Carbon|string|null $anchor = null): self
    {
        if (! array_key_exists($key, self::options())) {
            throw new InvalidArgumentException("Periode laporan tidak dikenal: {$key}");
        }

        $anchor = $anchor === null ? Carbon::today() : Carbon::parse($anchor);

        [$start, $end] = match ($key) {
            // One day only: 00:00 until 23:59 of the anchor date.
            self::HARIAN => [$anchor->copy()->startOfDay(), $anchor->copy()->endOfDay()],

            // A week runs Monday to Sunday, the Indonesian calendar week.
            self::MINGGUAN => [
                $anchor->copy()->startOfWeek(Carbon::MONDAY),
                $anchor->copy()->startOfWeek(Carbon::MONDAY)->addDays(6),
            ],

            // Everything inside the anchor's month.
            self::BULANAN => [$anchor->copy()->startOfMonth(), $anchor->copy()->endOfMonth()],

            // Everything inside the anchor's year.
            self::TAHUNAN => [$anchor->copy()->startOfYear(), $anchor->copy()->endOfYear()],
        };

        return new self($key, $start->startOfDay(), $end->startOfDay());
    }

    /**
     * "Harian", "Mingguan", "Bulanan" or "Tahunan".
     */
    public function label(): string
    {
        return self::options()[$this->key];
    }

    /**
     * The readable range: "7 Oktober 2026", "5 - 11 Oktober 2026", "Oktober 2026", "2026".
     */
    public function rangeLabel(): string
    {
        return match ($this->key) {
            self::HARIAN => $this->fullDate($this->start),
            self::MINGGUAN => $this->weekLabel(),
            self::BULANAN => self::monthLabel($this->start),
            self::TAHUNAN => (string) $this->start->year,
        };
    }

    /**
     * "Oktober 2026".
     *
     * The month names live in this class, so anything that needs to name a month
     * (the monthly statistics table, for instance) asks here instead of keeping
     * a second list of names that could drift out of step.
     */
    public static function monthLabel(Carbon $date): string
    {
        return self::BULAN[$date->month].' '.$date->year;
    }

    /**
     * First day of the period as a plain Y-m-d string, ready for a SQL comparison.
     */
    public function startDate(): string
    {
        return $this->start->toDateString();
    }

    /**
     * Last day of the period as a plain Y-m-d string, ready for a SQL comparison.
     */
    public function endDate(): string
    {
        return $this->end->toDateString();
    }

    /**
     * "5 - 11 Oktober 2026", but spelled out fully when the week crosses a month or a year.
     */
    private function weekLabel(): string
    {
        if ($this->start->month === $this->end->month && $this->start->year === $this->end->year) {
            return $this->start->day.' - '.$this->end->day.' '.self::BULAN[$this->start->month].' '.$this->start->year;
        }

        if ($this->start->year === $this->end->year) {
            return $this->start->day.' '.self::BULAN[$this->start->month].' - '.$this->fullDate($this->end);
        }

        return $this->fullDate($this->start).' - '.$this->fullDate($this->end);
    }

    /**
     * "7 Oktober 2026".
     */
    private function fullDate(Carbon $date): string
    {
        return $date->day.' '.self::BULAN[$date->month].' '.$date->year;
    }
}
