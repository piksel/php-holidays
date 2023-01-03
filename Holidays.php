<?php /** @noinspection SpellCheckingInspection */

declare(strict_types=1);

namespace Piksel\Holidays;

use DateTime;
use DateInterval;
use Exception;

final class Holidays {

    private static array $holidayDates = [];

    public static array $nonWorkDays = [
        "newyearsday"  => true,
        "epiphanyeve"  => false,
        "epiphanyday"  => true,
        "maundythrd"   => false,
        "goodfriday"   => true,
        "eastereve"    => true,
        "easterday"    => true,
        "easter2nd"    => true,
        "walpurgis"    => false,
        "mayfirst"     => true,
        "ascension"    => true,
        "penteceve"    => false,
        "pentecday"    => true,
        "nationalday"  => true,
        "midsumeve"    => true,
        "midsumday"    => true,
        "allsaintseve" => false,
        "allsaintsday" => true,
        "christmaseve" => true,
        "christmasday" => true,
        "christmas2nd" => true,
        "newyearseve"  => true
    ];

    /**
     * @throws Exception On invalid date
     */
    public static function getTomorrow($date): string
    {
        $d = new DateTime($date);
        $d->add(new DateInterval('P1D'));
        return $d->format('Y-m-d');
    }

    /**
     * @throws Exception On invalid date
     */
    public static function getOpenHours($oOH, $date, $holidays, $add_reason=false){
        $dt = new DateTime($date);
        $key = array_search($dt, $holidays);
        if ($key){
            return $oOH->{$key}->value.($add_reason?' ('.$oOH->{$key}->name.')':'');
        }
        else {
            return $oOH->{strtolower($dt->format('l'))}->value;
        }
    }

    public static function isNonWorkDay(string $holiday) {
        return self::$nonWorkDays[$holiday];
    }

    /**
     * @throws Exception On invalid date
     */
    public static function getHoliday($date): ?string
    {
        $d = is_string($date) ? new DateTime($date) : $date;
        $y = $d->format('Y');
        $datestr = $date->format('Y-m-d');
        foreach(self::getHolidays($y) as $hn => $hd) {
            if($hd->format('Y-m-d') == $datestr) return $hn;
        }
        return null;
    }

    /**
     * @throws Exception On invalid date
     */
    public static function isHoliday($date, &$holiday_id = null, &$workday = false): bool
    {
        $holiday_id = self::getHoliday($date);
        if($holiday_id != null){
            $workday = self::isNonWorkDay($holiday_id);
        }
        return $holiday_id != null;
    }

    /**
     * @throws Exception On invalid date
     */
    public static function getHolidayDate($holiday, $year = null){
        $y = $year ?? date('Y');
        return self::getHolidays($y)[$holiday];
    }

    /**
     * @throws Exception On invalid date
     */
    public static function getHolidayWeekday($holiday, $year = null): string
    {
        if(!array_key_exists($holiday, self::$nonWorkDays)){
            throw new Exception("Invalid holiday identifier '$holiday'");
        }
        $date = self::getHolidayDate($holiday, $year);
        return self::getWeekday($date);
    }

    public static function getWeekday($date): string
    {
        return strtolower($date->format('l'));
    }

    /**
     * @throws Exception On invalid date
     */
    public static function getHolidays($year = null) {

        $y = $year ?? date('Y');
        if(array_key_exists($y, self::$holidayDates)) {
            return self::$holidayDates[$y];
        }

        $p1d = new DateInterval('P1D');
        $h = array();

        $h['newyearsday'] = new DateTime($y.'-01-01');
        $h['epiphanyeve'] = new DateTime($y.'-01-05');
        $h['epiphanyday'] = new DateTime($y.'-01-06');

        list($h['maundythrd'],
            $h['goodfriday'],
            $h['eastereve'],
            $h['easterday'],
            $h['easter2nd'],
            $h['ascension'],
            $h['penteceve'],
            $h['pentecday']) = self::getEasterDays($y);

        $h['walpurgis']    = new DateTime($y.'-04-30');
        $h['mayfirst'] = new DateTime($y.'-05-01');
        $h['nationalday'] = new DateTime($y.'-06-06');

        // Midsommarafton
        // Rörligt datum, fredagen mellan 19 juni och 25 juni (fredagen före midsommardagen)
        $h['midsumeve'] = new DateTime($y.'-06-19');
        while($h['midsumeve']->format('N') != 5)
            $h['midsumeve']->add($p1d);

        // Midsommardagen
        // Rörligt datum, lördagen mellan 20 juni och 26 juni
        $h['midsumday'] = clone $h['midsumeve'];
        $h['midsumday']->add($p1d);

        // 1 nov Allhelgonaafton[b]
        // -4 Rörligt datum, fredag mellan 30 oktober och 5 november
        $h['allsaintseve'] = new DateTime($y.'-10-30');
        while($h['allsaintseve']->format('N') != 5)
            $h['allsaintseve']->add($p1d);

        // 2 nov Alla helgons dag
        // Rörligt datum, lördagen som infaller under perioden från 31 oktober till 6 november
        $h['allsaintsday'] = clone $h['allsaintseve'];
        $h['allsaintsday']->add($p1d);

        $h['christmaseve'] = new DateTime($y.'-12-24');
        $h['christmasday'] = new DateTime($y.'-12-25');
        $h['christmas2nd'] = new DateTime($y.'-12-26');

        $h['newyearseve'] = new DateTime($y.'-12-31');

        self::$holidayDates[$y] = $h;

        return $h;
    }

    /**
     * @throws Exception On invalid date
     */
    public static function getEasterDays($y): array
    {

        $p1d = new DateInterval('P1D');

        // Påskdagen
        // Rörligt datum, söndagen närmast efter den ecklesiastiska fullmåne som infaller på
        // eller närmast efter den 21 mars

        $ed = [
            '2012' => '04-08',
            '2013' => '03-31',
            '2014' => '04-20',
            '2015' => '04-05',
            '2016' => '03-27',
            '2017' => '04-16',
            '2018' => '04-01',
            '2019' => '04-21',
            '2020' => '04-12',
            '2021' => '04-04',
            '2022' => '04-17',
            '2023' => '04-09',
            '2024' => '03-31',
            '2025' => '04-20',
            '2026' => '04-05',
            '2027' => '03-28',
            '2028' => '04-16',
            '2029' => '04-01',
            '2030' => '04-21',
            '2031' => '04-13',
            '2032' => '03-28',
        ];

        $day = new DateTime($y.'-'.$ed[$y]);

        // Skärtorsdagen
        // Rörligt datum, torsdagen närmast före påskdagen
        $thd = clone $day;
        $thd->sub($p1d);
        while($thd->format('N') != 4)
            $thd->sub($p1d);

        // Långfredagen
        // Rörligt datum, fredagen närmast före påskdagen
        $frd = clone $thd;
        $frd->add($p1d);

        // Påskafton
        // Rörligt datum, dagen före påskdagen
        $eve = clone $day;
        $eve->sub($p1d);

        // Annandag påsk
        // Rörligt datum, dagen efter påskdagen
        $snd = clone $day;
        $snd->add($p1d);

        // Kristi himmelsfärdsdag
        // Rörligt datum, sjätte torsdagen efter påskdagen
        $asc = clone $day;
        $asc->add(new DateInterval('P5W'));
        $asc->add($p1d);
        while($asc->format('N') != 4)
            $asc->add($p1d);

        // Pingstdagen
        // Rörligt datum, sjunde söndagen efter påskdagen
        $pad = clone $day;
        $pad->add(new DateInterval('P6W'));
        $pad->add($p1d);
        while($pad->format('N') != 7)
            $pad->add($p1d);

        // Pingstafton
        // Rörligt datum, dagen före pingstdagen
        $pae = clone $pad;
        $pae->sub($p1d);

        return array($thd, $frd, $eve, $day, $snd, $asc, $pae, $pad);
    }

}

