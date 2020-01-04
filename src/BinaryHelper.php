<?php

declare(strict_types=1);

namespace PBurggraf\FileHandler;

use PBurggraf\FileHandler\Exception\ValueExceededException;

/**
 * @author Philip Burggraf <philip@pburggraf.de>
 */
class BinaryHelper
{
    /**
     * @param int $byte
     * @param int $position
     *
     * @throws ValueExceededException
     *
     * @return bool
     */
    public static function isBitSet(int $byte, int $position): bool
    {
        if ($byte > 0xff || $byte < 0) {
            throw new ValueExceededException('Byte value exceeded');
        }

        return ($byte & 1 << ($position - 1)) >> ($position - 1) === 1;
    }

    /**
     * @param int $integer
     *
     * @return array<int>
     */
    public static function asByteArray(int $integer): array
    {
        return [
            $integer & 0xff000000,
            $integer & 0xff0000,
            $integer & 0xff00,
            $integer & 0xff,
        ];
    }

    /**
     * @param int $byte1
     * @param int $byte2
     *
     * @throws ValueExceededException
     *
     * @return int
     */
    public static function asShort(int $byte1, int $byte2): int
    {
        if ($byte1 > 0xff || $byte2 > 0xff || $byte1 < 0 || $byte2 < 0) {
            throw new ValueExceededException('Byte value exceeded');
        }

        return ($byte1 << 8) + $byte2;
    }

    /**
     * @param int $byte1
     * @param int $byte2
     * @param int $byte3
     * @param int $byte4
     *
     * @throws ValueExceededException
     *
     * @return int
     */
    public static function asInteger(int $byte1, int $byte2, int $byte3, int $byte4): int
    {
        if ($byte1 > 0xff || $byte2 > 0xff || $byte3 > 0xff || $byte4 > 0xff || $byte1 < 0 || $byte2 < 0 || $byte3 < 0 || $byte4 < 0) {
            throw new ValueExceededException('Byte value exceeded');
        }

        return ($byte1 << 24) + ($byte2 << 16) + ($byte3 << 8) + $byte4;
    }

    /**
     * @param int $byte
     *
     * @throws ValueExceededException
     *
     * @return array<int>
     */
    public static function extractNibblesFromByte(int $byte): array
    {
        if ($byte > 0xff || $byte < 0) {
            throw new ValueExceededException('Byte value exceeded');
        }

        return [
            ($byte & 0xf0) >> 4,
            $byte & 0x0f,
        ];
    }

    /**
     * @param int $short
     *
     * @throws ValueExceededException
     *
     * @return array<int>
     */
    public static function extractNibblesFromShort(int $short): array
    {
        return array_merge(
            self::extractNibblesFromByte(($short & 0xff00) >> 8),
            self::extractNibblesFromByte($short & 0x00ff)
        );
    }

    /**
     * @param int $integer
     *
     * @throws ValueExceededException
     *
     * @return array<int>
     */
    public static function extractNibblesFromInteger(int $integer): array
    {
        return array_merge(
            self::extractNibblesFromShort(($integer & 0xffff0000) >> 16),
            self::extractNibblesFromShort($integer & 0x0000ffff)
        );
    }
}
