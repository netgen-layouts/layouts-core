<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

/**
 * Filter used to remove all unsafe HTML from the provided value.
 */
final class HtmlPurifier
{
    private HtmlSanitizerInterface $sanitizer;

    public function __construct(
        ?HtmlSanitizerInterface $sanitizer = null,
    ) {
        $this->sanitizer = $sanitizer ?? new HtmlSanitizer(
            new HtmlSanitizerConfig()->allowSafeElements(),
        );
    }

    public function purify(string $value): string
    {
        return $this->sanitizer->sanitize($value);
    }
}
