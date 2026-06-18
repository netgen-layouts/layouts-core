<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils;

use ReflectionClass;
use Symfony\Component\VarExporter\Hydrator;

use function array_map;
use function count;
use function function_exists;

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
            return self::hydrate(new static(), $data);
        }

        $reflector = new ReflectionClass(static::class);

        /** @var static $object */
        $object = $reflector->newLazyGhost(
            static function (object $object) use ($lazyInitializers): void {
                $lazyData = array_map(
                    static fn (callable $initializer): mixed => $initializer($object),
                    $lazyInitializers,
                );

                self::hydrate($object, $lazyData);
            },
        );

        foreach ($data as $property => $value) {
            $reflector->getProperty($property)->setRawValueWithoutLazyInitialization($object, $value);
        }

        return $object;
    }

    /**
     * Sets the provided properties on the given object.
     *
     * Symfony 8.1 deprecated Symfony\Component\VarExporter\Hydrator in favor of
     * the deepclone_hydrate() function (provided by the deepclone extension or
     * the symfony/polyfill-deepclone polyfill that symfony/var-exporter pulls in
     * since 8.1). Use it directly when available, and fall back to the Hydrator
     * for symfony/var-exporter ^7.4 || ^8.0 where the function does not exist.
     *
     * @template T of object
     *
     * @param T $object
     * @param array<string, mixed> $data
     *
     * @return T
     */
    private static function hydrate(object $object, array $data): object
    {
        if (function_exists('deepclone_hydrate')) {
            return deepclone_hydrate($object, $data, DEEPCLONE_HYDRATE_PRESERVE_REFS);
        }

        return Hydrator::hydrate($object, $data);
    }
}
