<?php

namespace Netgen\BlockManager\Exception\Core;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class TranslationException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $locale
     *
     * @return \Netgen\BlockManager\Exception\Core\TranslationException
     */
    public static function noTranslation($locale)
    {
        return new self(
            sprintf(
                'Translation with "%s" locale does not exist.',
                $locale
            )
        );
    }
}
