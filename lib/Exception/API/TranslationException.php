<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\API;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

final class TranslationException extends InvalidArgumentException implements Exception
{
    public static function noTranslation(string $locale): self
    {
        return new self(
            sprintf(
                'Translation with "%s" locale does not exist.',
                $locale
            )
        );
    }
}
