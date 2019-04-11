<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Locale;

use Netgen\Layouts\Exception\Exception;
use RuntimeException;

final class LocaleException extends RuntimeException implements Exception
{
    public static function noLocale(): self
    {
        return new self('No locales available in the current context.');
    }
}
