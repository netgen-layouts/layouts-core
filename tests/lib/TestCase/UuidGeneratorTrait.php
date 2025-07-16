<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

use function array_map;
use function count;
use function method_exists;

trait UuidGeneratorTrait
{
    /**
     * @param array<int, string> $uuids
     *
     * @return mixed
     */
    private function withUuids(callable $callable, array $uuids)
    {
        $uuids = array_map(
            static fn (string $uuid): UuidInterface => Uuid::fromString($uuid),
            $uuids,
        );

        $originalFactory = Uuid::getFactory();
        $factoryMock = $this->createMock(UuidFactoryInterface::class);

        if (method_exists(UuidFactoryInterface::class, 'getValidator')) {
            $factoryMock
                ->method('getValidator')
                ->willReturn($originalFactory->getValidator());
        }

        $factoryMock
            ->method('fromString')
            ->willReturnCallback(
                static fn (string $uuid): UuidInterface => $originalFactory->fromString($uuid),
            );

        $factoryMock->expects(self::exactly(count($uuids)))
            ->method('uuid4')
            ->willReturnOnConsecutiveCalls(...$uuids);

        Uuid::setFactory($factoryMock);

        try {
            return $callable();
        } finally {
            Uuid::setFactory($originalFactory);
        }
    }
}
