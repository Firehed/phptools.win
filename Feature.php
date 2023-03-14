<?php

declare(strict_types=1);

readonly class Feature
{
    public string $name;

    public function __construct(
        public Version $version,
        public array $categories,
        string $name,
        public string $rfc,
        public array $docs,
    ) {
        $this->name = $this->backticksToCode($name);
    }

    private function backticksToCode(string $text): string
    {
        if (!str_contains($text, '`')) {
            return $text;
        }
        // EXTREMELY crude string parser:
        // `foo` => <code>foo</code>
        // \` => `
        // \\ => \

        $output = $currentCode = '';
        $inCode = false;
        $escaped = false;
        // FIXME: mbstring
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];

            if ($escaped) {
                $escaped = false;
                $output .= $char;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
            } elseif ($char === '`') {
                if ($inCode) {
                    // Transfer to HTML code format
                    $output .= '<code class="language-php">' . $currentCode . '</code>';
                    // Turn off flags
                    $inCode = false;
                    $currentCode = '';
                } else {
                    $inCode = true;
                }
            } else {
                if ($inCode) {
                    $currentCode .= $char;
                } else {
                    $output .= $char;
                }
            }
        }

        return $output;
    }
}
