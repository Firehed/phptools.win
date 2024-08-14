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

    public function isActivelySupportedVersion(): bool
    {
        return match ($this) {
            Version::v8_1 => true,
            Version::v8_2 => true,
            Version::v8_3 => true,
            default => false,
        };
    }
}
