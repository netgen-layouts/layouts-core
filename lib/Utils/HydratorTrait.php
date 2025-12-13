<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils;

use ReflectionClass;
use Symfony\Component\VarExporter\Hydrator;

use function array_map;
use function count;

trait HydratorTrait
{
    final public function __construct() {}

    /**
     * Creates a new instance of a class on which the method is called
     * and return the object hydrated with provided data.
     *
     * @param array<string, mixed> $data
     * @param array<string, callable> $lazyInitializers
     */
    final public static function fromArray(array $data, array $lazyInitializers = []): static
    {
        if (count($lazyInitializers) === 0) {
            return Hydrator::hydrate(new static(), $data);
        }

        $reflector = new ReflectionClass(static::class);

        /** @var static $object */
        $object = $reflector->newLazyGhost(
            static function (object $object) use ($lazyInitializers): void {
                $lazyData = array_map(
                    static fn (callable $initializer): mixed => $initializer($object),
                    $lazyInitializers,
                );

                Hydrator::hydrate($object, $lazyData);
            },
        );

        foreach ($data as $property => $value) {
            $reflector->getProperty($property)->setRawValueWithoutLazyInitialization($object, $value);
        }

        return $object;
    }
}
