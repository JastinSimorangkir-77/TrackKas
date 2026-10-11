<?php

namespace Tests\Unit;

use App\Support\ReportPeriod;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ReportPeriodTest extends TestCase
{
    public function test_the_daily_period_covers_one_day(): void
    {
        $period = ReportPeriod::make(ReportPeriod::HARIAN, '2026-10-07');

        $this->assertSame('2026-10-07', $period->startDate());
        $this->assertSame('2026-10-07', $period->endDate());
        $this->assertSame('7 Oktober 2026', $period->rangeLabel());
        $this->assertSame('Harian', $period->label());
    }

    public function test_the_weekly_period_runs_from_monday_to_sunday(): void
    {
        // 7 October 2026 is a Wednesday, so the week is 5 - 11 October.
        $period = ReportPeriod::make(ReportPeriod::MINGGUAN, '2026-10-07');

        $this->assertSame('2026-10-05', $period->startDate());
        $this->assertSame('2026-10-11', $period->endDate());
        $this->assertSame('5 - 11 Oktober 2026', $period->rangeLabel());
    }

    public function test_a_week_that_crosses_a_month_is_labelled_with_both_months(): void
    {
        // 1 October 2026 is a Thursday, so its week starts on 28 September.
        $period = ReportPeriod::make(ReportPeriod::MINGGUAN, '2026-10-01');

        $this->assertSame('2026-09-28', $period->startDate());
        $this->assertSame('2026-10-04', $period->endDate());
        $this->assertSame('28 September - 4 Oktober 2026', $period->rangeLabel());
    }

    public function test_the_monthly_period_covers_the_whole_month(): void
    {
        $period = ReportPeriod::make(ReportPeriod::BULANAN, '2026-10-20');

        $this->assertSame('2026-10-01', $period->startDate());
        $this->assertSame('2026-10-31', $period->endDate());
        $this->assertSame('Oktober 2026', $period->rangeLabel());
    }

    public function test_the_yearly_period_covers_the_whole_year(): void
    {
        $period = ReportPeriod::make(ReportPeriod::TAHUNAN, '2026-06-15');

        $this->assertSame('2026-01-01', $period->startDate());
        $this->assertSame('2026-12-31', $period->endDate());
        $this->assertSame('2026', $period->rangeLabel());
    }

    public function test_an_unknown_period_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ReportPeriod::make('per-jam');
    }
}
