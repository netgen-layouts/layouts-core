<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Locale;

use Netgen\BlockManager\Exception\Exception;
use RuntimeException;

final class LocaleException extends RuntimeException implements Exception
{
    /**
     * @return \Netgen\BlockManager\Exception\Locale\LocaleException
     */
    public static function noLocale()
    {
        return new self('No locales available in the current context.');
    }
}
