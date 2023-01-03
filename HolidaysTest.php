<?php

declare(strict_types=1);

namespace Piksel\Holidays;

use PHPUnit\Framework\TestCase;

class HolidaysTest extends TestCase
{

    public function testIsHoliday()
    {
        $this->markTestSkipped();
    }

    public function testGetEasterDays()
    {
        $this->markTestSkipped();
    }

    public function testGetOpenHours()
    {
        $this->markTestSkipped();
    }

    public function testGetHolidayDate()
    {
        $this->markTestSkipped();
    }

    public function testGetTomorrow()
    {
        $this->markTestSkipped();
    }

    public function testGetHoliday()
    {
        $this->markTestSkipped();
    }

    public function testGetHolidayWeekday()
    {
        $this->markTestSkipped();
    }

    public function testGetHolidays()
    {
        // Assert 2023
        $expected = [
            '2023-01-01', // Nyårsdagen
            '2023-01-06', // Trettondedag jul
            '2023-04-07', // Långfredagen
            '2023-04-08', // Påskafton (dag innan röd)
            '2023-04-09', // Påskdagen
            '2023-04-10', // Annandag påsk
            '2023-05-01', // Första maj
            '2023-05-18', // Kristi himmelfärdsdag
            '2023-05-28', // Pingstdagen
            '2023-06-06', // Sveriges nationaldag
            '2023-06-23', // Midsommarafton (dag innan röd)
            '2023-06-24', // Midsommardagen
            '2023-11-04', // Alla helgons dag
            '2023-12-24', // Julafton (dag innan röd)
            '2023-12-25', // Juldagen
            '2023-12-26', // Annandag jul
            '2023-12-31', // Nyårsafton (dag innan röd)
        ];
        $all_holidays = Holidays::getHolidays(2023);
        $non_work_days = array_filter($all_holidays, fn($v, $k): bool => Holidays::isNonWorkDay($k), ARRAY_FILTER_USE_BOTH);
        $actual = array_map(fn(\DateTime $d): string => $d->format('Y-m-d'), array_values($non_work_days));
        sort($actual);
        $this->assertEquals($expected,  $actual);
    }

    public function testGetWeekday()
    {
        $this->markTestSkipped();
    }

    public function testIsNonWorkDay()
    {
        $this->markTestSkipped();
    }
}
