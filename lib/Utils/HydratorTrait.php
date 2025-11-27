<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils;

use ReflectionClass;

use function array_map;
use function count;

trait HydratorTrait
{
    private static Hydrator $__hydrator;

    final public function __construct() {}

    /**
     * Creates a new instance of a class on which the method is called
     * and return the object hydrated with provided data.
     *
     * @param array<string, mixed> $data
     * @param array<string, callable> $lazyInitializers
     */
    public static function fromArray(array $data, array $lazyInitializers = []): static
    {
        self::initHydrator();

        if (count($lazyInitializers) === 0) {
            return self::$__hydrator->hydrate($data, new static());
        }

        $reflector = new ReflectionClass(static::class);

        /** @var static $object */
        $object = $reflector->newLazyGhost(
            static function (object $object) use ($lazyInitializers): void {
                $lazyData = array_map(
                    static fn (callable $initializer) => $initializer($object),
                    $lazyInitializers,
                );

                self::$__hydrator->hydrate($lazyData, $object);
            },
        );

        foreach ($data as $property => $value) {
            $reflector->getProperty($property)->setRawValueWithoutLazyInitialization($object, $value);
        }

        return $object;
    }

    /**
     * Hydrates the object instance with provided data.
     *
     * @param array<string, mixed> $data
     */
    public function hydrate(array $data): static
    {
        self::initHydrator();

        return self::$__hydrator->hydrate($data, $this);
    }

    /**
     * Initializes the hydrator in case it was not initialized yet.
     */
    private static function initHydrator(): void
    {
        self::$__hydrator ??= new Hydrator();
    }
}
