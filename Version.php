<?php

declare(strict_types=1);

enum Version: string
{
    case v7_0 = '7.0';
    case v7_1 = '7.1';
    case v7_2 = '7.2';
    case v7_3 = '7.3';
    case v7_4 = '7.4';
    case v8_0 = '8.0';
    case v8_1 = '8.1';
    case v8_2 = '8.2';
    case v8_3 = '8.3';
    case v8_4 = '8.4';
    case v8_5 = '8.5';

    public const CURRENT = [self::v8_2, self::v8_3, self::v8_4];
    public const UPCOMING = self::v8_5;

    public function isAddedInCurrent(): bool
    {
        return in_array($this, self::CURRENT);
    }

    public function isUpcoming(): bool
    {
        return $this === self::UPCOMING;
    }

    public function isSupportedInVersion(Version $version): bool
    {
        return version_compare($this->value, $version->value, '<=');
    }
}
