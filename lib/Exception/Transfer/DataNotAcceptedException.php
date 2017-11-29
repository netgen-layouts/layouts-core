<?php

namespace Netgen\BlockManager\Exception\Transfer;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

/**
 * Exception thrown by Importer when the provided data is not accepted for import.
 *
 * @see \Netgen\BlockManager\Transfer\Input\Importer
 */
final class DataNotAcceptedException extends InvalidArgumentException implements Exception
{
    /**
     * Thrown when no format information is found.
     *
     * @return \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException
     */
    public static function noFormatInformation()
    {
        return new self(
            'Could not find format information in the provided data.'
        );
    }

    /**
     * Thrown when format type is not accepted.
     *
     * @param string $expected
     * @param string $actual
     *
     * @return \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException
     */
    public static function typeNotAccepted($expected, $actual)
    {
        return new self(
            sprintf(
                'Supported type is "%s", type "%s" was given.',
                $expected,
                $actual
            )
        );
    }

    /**
     * Thrown when format version is not accepted.
     *
     * @param string $expected
     * @param string $actual
     *
     * @return \Netgen\BlockManager\Exception\Transfer\DataNotAcceptedException
     */
    public static function versionNotAccepted($expected, $actual)
    {
        return new self(
            sprintf(
                'Supported version is "%s", version "%s" was given.',
                $expected,
                $actual
            )
        );
    }
}
