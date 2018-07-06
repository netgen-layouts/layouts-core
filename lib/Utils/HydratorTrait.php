<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Utils;

trait HydratorTrait
{
    /**
     * @var \Zend\Hydrator\HydratorInterface
     */
    private static $__hydrator;

    private function hydrate(array $data): void
    {
        self::$__hydrator = self::$__hydrator ?? new Hydrator();
        self::$__hydrator->hydrate($data, $this);
    }
}
