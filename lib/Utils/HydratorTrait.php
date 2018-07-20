<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Utils;

trait HydratorTrait
{
    /**
     * @var \Zend\Hydrator\HydratorInterface
     */
    private static $__hydrator;

    /**
     * Creates a new instance of a class on which the method is called
     * and return the object hydrated with provided data.
     *
     * @param array $data
     *
     * @return self
     */
    public static function fromArray(array $data)
    {
        self::initHydrator();

        return self::$__hydrator->hydrate($data, new self());
    }

    /**
     * Hydrates the object instance with provided data.
     */
    public function hydrate(array $data): void
    {
        self::initHydrator();

        self::$__hydrator->hydrate($data, $this);
    }

    /**
     * Initializes the hydrator in case it was not initialized yet.
     */
    private static function initHydrator(): void
    {
        self::$__hydrator = self::$__hydrator ?? new Hydrator();
    }
}
