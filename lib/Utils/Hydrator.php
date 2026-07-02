<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils;

use function deepclone_hydrate;

/**
 * Thin wrapper around deepclone_hydrate(), provided by the deepclone
 * extension or the symfony/polyfill-deepclone polyfill.
 *
 * Used instead of Symfony\Component\VarExporter\Hydrator, which was
 * deprecated in Symfony 8.1 in favor of deepclone_hydrate().
 */
final class Hydrator
{
    /**
     * Sets the provided properties on the given object.
     *
     * @template T of object
     *
     * @param T $object
     * @param array<string, mixed> $data
     *
     * @return T
     */
    public static function hydrate(object $object, array $data): object
    {
        return deepclone_hydrate($object, $data, DEEPCLONE_HYDRATE_PRESERVE_REFS);
    }
}
