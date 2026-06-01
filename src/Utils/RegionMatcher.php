<?php

declare(strict_types=1);

namespace App\Utils;

use InvalidArgumentException;

class RegionMatcher
{
    /**
     * New Riot display regions -> old platform routing
     */
    private const REGION_TO_PLATFORM = [
        'EUNE' => 'EUN1',
        'EUW'  => 'EUW1',
        'NA'   => 'NA1',
        'KR'   => 'KR',
        'JP'   => 'JP1',
        'BR'   => 'BR1',
        'LAN'  => 'LA1',
        'LAS'  => 'LA2',
        'OCE'  => 'OC1',
        'RU'   => 'RU',
        'TR'   => 'TR1',
        'MENA' => 'ME1',
        'SEA'  => 'SG2',
        'TW'   => 'TW2',
        'VN'   => 'VN2',
    ];

    /**
     * Platform routing -> regional routing
     */
    private const PLATFORM_TO_ROUTING = [
        'EUN1' => 'europe',
        'EUW1' => 'europe',
        'TR1'  => 'europe',
        'RU'   => 'europe',
        'ME1'  => 'europe',

        'KR'   => 'asia',
        'JP1'  => 'asia',
        'SG2'  => 'asia',
        'TW2'  => 'asia',
        'VN2'  => 'asia',

        'BR1'  => 'americas',
        'LA1'  => 'americas',
        'LA2'  => 'americas',
        'NA1'  => 'americas',
        'OC1'  => 'americas',
    ];

    public static function isValidRegionOrPlatform(string $value): bool
    {
        $value = strtoupper(trim($value));

        return isset(self::REGION_TO_PLATFORM[$value])
            || isset(self::PLATFORM_TO_ROUTING[$value]);
    }

    public static function matchRegionToServer(string $value): string
    {
        return self::anyToRouting($value);
    }

    /**
     * EUNE -> EUN1
     * EUW -> EUW1
     * NA -> NA1
     */
    public static function regionToPlatform(string $region): string
    {
        $region = strtoupper(trim($region));

        if (!isset(self::REGION_TO_PLATFORM[$region])) {
            throw new InvalidArgumentException(sprintf(
                'Nieprawidłowy region Riot: "%s". Dozwolone: %s',
                $region,
                implode(', ', array_keys(self::REGION_TO_PLATFORM))
            ));
        }

        return self::REGION_TO_PLATFORM[$region];
    }

    public static function anyToPlatform(string $value): string
    {
        $value = strtoupper(trim($value));

        if (isset(self::REGION_TO_PLATFORM[$value])) {
            return self::REGION_TO_PLATFORM[$value];
        }

        if (isset(self::PLATFORM_TO_ROUTING[$value])) {
            return $value;
        }

        throw new InvalidArgumentException(sprintf(
            'NieprawidĹ‚owy region/platforma Riot: "%s"',
            $value
        ));
    }

    /**
     * EUN1 -> europe
     * NA1 -> americas
     * KR -> asia
     */
    public static function platformToRouting(string $platform): string
    {
        $platform = strtoupper(trim($platform));

        if (!isset(self::PLATFORM_TO_ROUTING[$platform])) {
            throw new InvalidArgumentException(sprintf(
                'Nieprawidłowa platforma Riot: "%s". Dozwolone: %s',
                $platform,
                implode(', ', array_keys(self::PLATFORM_TO_ROUTING))
            ));
        }

        return self::PLATFORM_TO_ROUTING[$platform];
    }

    /**
     * EUNE -> europe
     * EUW -> europe
     * NA -> americas
     */
    public static function regionToRouting(string $region): string
    {
        $platform = self::regionToPlatform($region);

        return self::platformToRouting($platform);
    }

    /**
     * Optional helper:
     * accepts BOTH EUNE and EUN1
     */
    public static function anyToRouting(string $value): string
    {
        $value = strtoupper(trim($value));

        if (isset(self::REGION_TO_PLATFORM[$value])) {
            $value = self::REGION_TO_PLATFORM[$value];
        }

        if (!isset(self::PLATFORM_TO_ROUTING[$value])) {
            throw new InvalidArgumentException(sprintf(
                'Nieprawidłowy region/platforma Riot: "%s"',
                $value
            ));
        }

        return self::PLATFORM_TO_ROUTING[$value];
    }
}
