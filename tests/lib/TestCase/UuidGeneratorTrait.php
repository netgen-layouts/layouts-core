<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_map;

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
        $uuids = array_map(Uuid::fromString(...), $uuids);

        $originalFactory = Uuid::getFactory();
        $factoryStub = self::createStub(UuidFactoryInterface::class);

        $factoryStub
            ->method('getValidator')
            ->willReturn($originalFactory->getValidator());

        $factoryStub
            ->method('fromString')
            ->willReturnCallback(
                static fn (string $uuid): UuidInterface => $originalFactory->fromString($uuid),
            );

        $factoryStub
            ->method('uuid4')
            ->willReturnOnConsecutiveCalls(...$uuids);

        Uuid::setFactory($factoryStub);

        try {
            return $callable();
        } finally {
            Uuid::setFactory($originalFactory);
        }
    }
}
