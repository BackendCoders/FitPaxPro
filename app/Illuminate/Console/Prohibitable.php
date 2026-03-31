<?php

namespace Illuminate\Console;

trait Prohibitable
{
    protected static bool $prohibited = false;

    public static function prohibit(bool $prohibit = true): void
    {
        static::$prohibited = $prohibit;
    }

    public function isProhibited(): bool
    {
        return static::$prohibited;
    }
}
