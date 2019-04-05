<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Utils;

use Netgen\BlockManager\Exception\RuntimeException;

final class Hydrator
{
    /**
     * Extract values from an object.
     *
     * @param object $object
     *
     * @return array<string, mixed>
     */
    public function extract($object): array
    {
        if (!is_object($object)) {
            throw new RuntimeException(
                sprintf('%s expects the provided $object to be a PHP object', __METHOD__)
            );
        }

        return (function (): array {
            return get_object_vars($this);
        })->call($object);
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array<string, mixed> $data
     * @param object $object
     *
     * @return mixed
     */
    public function hydrate(array $data, $object)
    {
        if (!is_object($object)) {
            throw new RuntimeException(
                sprintf('%s expects the provided $object to be a PHP object', __METHOD__)
            );
        }

        return (function (array $data) {
            foreach ($data as $property => $value) {
                if (!property_exists($this, $property)) {
                    throw new RuntimeException(
                        sprintf(
                            'Property "%s" does not exist in "%s" class.',
                            $property,
                            get_class($this)
                        )
                    );
                }

                $this->{$property} = $value;
            }

            return $this;
        })->call($object, $data);
    }
}
