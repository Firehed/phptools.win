<?php

declare(strict_types=1);

readonly class Feature
{
    public function __construct(
        public Version $version,
        public array $categories,
        public string $name,
        public string $rfc,
        public array $docs,
    ) {
    }
}
