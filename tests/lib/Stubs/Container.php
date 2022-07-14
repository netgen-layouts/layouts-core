<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Stubs;

use Psr\Container\ContainerInterface;

use function array_key_exists;

final class Container implements ContainerInterface
{
    /**
     * @var mixed[]
     */
    private array $entries;

    /**
     * @param mixed[] $entries
     */
    public function __construct(array $entries = [])
    {
        $this->entries = $entries;
    }

    public function get($id)
    {
        return $this->entries[$id];
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->entries);
    }
}
