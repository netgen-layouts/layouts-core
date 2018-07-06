<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Utils;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Exception\RuntimeException;
use Zend\Hydrator\HydratorInterface;

final class Hydrator implements HydratorInterface
{
    public function extract($object): array
    {
        if (!is_object($object)) {
            throw new RuntimeException(
                sprintf('%s expects the provided $object to be a PHP object', __METHOD__)
            );
        }

        return (function (): array { return get_object_vars($this); })->call($object);
    }

    public function hydrate(array $data, $object)
    {
        if (!is_object($object)) {
            throw new RuntimeException(
                sprintf('%s expects the provided $object to be a PHP object', __METHOD__)
            );
        }

        (function () use ($data): void {
            foreach ($data as $property => $value) {
                if (!property_exists($this, $property)) {
                    throw new InvalidArgumentException(
                        'properties',
                        sprintf(
                            'Property "%s" does not exist in "%s" class.',
                            $property,
                            get_class($this)
                        )
                    );
                }

                $this->{$property} = $value;
            }
        })->call($object);

        return $object;
    }
}
