<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Stubs;

use Psr\Container\ContainerInterface;

use function array_key_exists;

final class Container implements ContainerInterface
{
    /**
     * @param mixed[] $entries
     */
    public function __construct(
        private array $entries = [],
    ) {}

    public function get(string $id): object
    {
        return $this->entries[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }
}
