<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

trait UuidGeneratorTrait
{
    /**
     * @template T
     *
     * @param callable(): T $callable
     * @param array<int, string> $uuids
     *
     * @return T
     */
    private function withUuids(callable $callable, array $uuids): mixed
    {
        $this->uuidFactory = new MockUuidFactory($uuids);

        $this->createHandlers();

        return $callable();
    }
}
