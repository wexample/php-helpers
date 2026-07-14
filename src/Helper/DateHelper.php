<?php

namespace Wexample\Helpers\Helper;

class DateHelper
{
    public const DATE_PATTERN_PART_YEAR_FULL = 'Y';
    public const DATE_PATTERN_PART_MONTH_FULL = 'm';
    public const DATE_PATTERN_PART_DAY_FULL = 'd';
    public const DATE_PATTERN_PART_HOURS_FULL = 'H';
    public const DATE_PATTERN_PART_MINUTES_FULL = 'i';
    public const DATE_PATTERN_PART_SECONDS_FULL = 's';
    public const DATE_PATTERN_DAY_DEFAULT =
        self::DATE_PATTERN_PART_YEAR_FULL.'-'.
        self::DATE_PATTERN_PART_MONTH_FULL.'-'.
        self::DATE_PATTERN_PART_DAY_FULL;
    public const DATE_PATTERN_DAY_REVERTED =
        self::DATE_PATTERN_PART_YEAR_FULL.'-'.
        self::DATE_PATTERN_PART_DAY_FULL.'-'.
        self::DATE_PATTERN_PART_MONTH_FULL;
    public const DATE_PATTERN_YMD_FR =
        self::DATE_PATTERN_PART_DAY_FULL.'/'.
        self::DATE_PATTERN_PART_MONTH_FULL.'/'.
        self::DATE_PATTERN_PART_YEAR_FULL;
    public const DATE_PATTERN_MICROTIME_DEFAULT = self::DATE_PATTERN_TIME_DEFAULT.'.u';
    public const TIME_PATTERN_SECOND_DEFAULT = self::DATE_PATTERN_PART_HOURS_FULL.':'.self::DATE_PATTERN_PART_MINUTES_FULL.':'.self::DATE_PATTERN_PART_SECONDS_FULL;
    public const DATE_PATTERN_TIME_ZULU = self::DATE_PATTERN_DAY_DEFAULT.'\T'.self::TIME_PATTERN_SECOND_DEFAULT.'p';
    public const DATE_PATTERN_TIME_DEFAULT = self::DATE_PATTERN_DAY_DEFAULT.' '.self::TIME_PATTERN_SECOND_DEFAULT;
    public const DATE_PATTERN_TIME_REVERTED = self::DATE_PATTERN_DAY_REVERTED.' '.self::TIME_PATTERN_SECOND_DEFAULT;
    public const DATE_PATTERN_ISO08601 = self::DATE_PATTERN_DAY_DEFAULT.'\T'.self::TIME_PATTERN_SECOND_DEFAULT;
    // @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/
    public const INTL_DATE_FORMATTER_MONTH_FULL = 'MMMM';
    public const INTL_DATE_FORMATTER_YEAR_FULL = 'YYYY';
    public const INTL_DATE_PATTERN_MONTH_AND_YEAR_FULL =
        self::INTL_DATE_FORMATTER_MONTH_FULL
        .' '.self::INTL_DATE_FORMATTER_YEAR_FULL;
    public const QUERY_STRING_DATE_FORMATS = [
        self::DATE_PATTERN_TIME_DEFAULT,
        'Y-m-d H:i',
        'Y-m-d H',
        'Y-m-d',
        'Y-m',
        self::DATE_PATTERN_PART_YEAR_FULL,
    ];
}
