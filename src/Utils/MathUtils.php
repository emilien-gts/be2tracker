<?php

declare(strict_types=1);

namespace App\Utils;

class MathUtils
{
    public const int DEFAULT_SCALE = 3;

    public static function add(string $number1, string $number2, int $scale = self::DEFAULT_SCALE): string
    {
        return self::mathOperation('bcadd', $number1, $number2, $scale);
    }

    public static function sub(string $number1, string $number2, int $scale = self::DEFAULT_SCALE): string
    {
        return self::mathOperation('bcsub', $number1, $number2, $scale);
    }

    public static function div(string $number1, string $number2, int $scale = self::DEFAULT_SCALE): string
    {
        return self::mathOperation('bcdiv', $number1, $number2, $scale);
    }

    public static function mul(string $number1, string $number2, int $scale = self::DEFAULT_SCALE): string
    {
        return self::mathOperation('bcmul', $number1, $number2, $scale);
    }

    public static function eq(string $number1, string $number2, int $scale = self::DEFAULT_SCALE): bool
    {
        return 0 === self::comp($number1, $number2, $scale);
    }

    public static function gt(string $number1, string $number2, int $scale = self::DEFAULT_SCALE): bool
    {
        return 1 === self::comp($number1, $number2, $scale);
    }

    public static function comp(string $number1, string $number2, int $scale = self::DEFAULT_SCALE): int
    {
        return \bccomp($number1, $number2, $scale);
    }

    public static function percentage(string $part, string $whole, int $scale = self::DEFAULT_SCALE): string
    {
        if (self::eq($whole, '0.0')) {
            throw new \InvalidArgumentException('The whole value cannot be zero.');
        }

        $percentage = self::div($part, $whole, $scale);

        return self::mul($percentage, '100', $scale);
    }

    public static function round(string $number, int $precision = 0, int $mode = \PHP_ROUND_HALF_UP): string
    {
        if ($precision < 0) {
            throw new \OutOfBoundsException(\sprintf('Precision %d is out of bounds', $precision));
        }

        $theRounderNumber = match ($mode) {
            \PHP_ROUND_HALF_UP => '5',
            \PHP_ROUND_HALF_DOWN => '4',
            default => throw new \OutOfBoundsException(\sprintf('Mode %d is out of bounds', $mode)),
        };

        $facteur = \bcpow('10', (string) (1 + $precision));
        $numberToAddForRound = \bcdiv($theRounderNumber, $facteur, $precision + 1);
        if (1 === self::comp('0.0', $number, $precision)) {
            return \bcsub($number, $numberToAddForRound, $precision);
        }

        return \bcadd($number, $numberToAddForRound, $precision);
    }

    private static function mathOperation(
        callable $bcMathCallable,
        string $number1,
        string $number2,
        int $scale = self::DEFAULT_SCALE,
    ): string {
        return self::round(\call_user_func($bcMathCallable, $number1, $number2, $scale + 1), $scale);
    }
}
