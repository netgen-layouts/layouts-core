<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils;

use function deepclone_hydrate;

use const DEEPCLONE_HYDRATE_PRESERVE_REFS;

final class Hydrator
{
    /**
     * Hydrates the provided object with given properties.
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
        /** @var T $return */
        $return = deepclone_hydrate($object, $data, DEEPCLONE_HYDRATE_PRESERVE_REFS);

        return $return;
    }
}
