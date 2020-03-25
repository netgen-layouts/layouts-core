<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

trait UuidGeneratorTrait
{
    /**
     * @param callable $callable
     * @param string[] $uuids
     *
     * @return mixed
     */
    private function withUuids(callable $callable, array $uuids)
    {
        $uuids = array_map(
            static function (string $uuid): UuidInterface {
                return Uuid::fromString($uuid);
            },
            $uuids
        );

        $originalFactory = Uuid::getFactory();

        $factoryMock = $this->createMock(UuidFactoryInterface::class);

        $factoryMock->expects(self::any())
            ->method('getValidator')
            ->willReturn($originalFactory->getValidator());

        $factoryMock->expects(self::any())
            ->method('fromString')
            ->willReturnCallback(
                static function (string $uuid) use ($originalFactory): UuidInterface {
                    return $originalFactory->fromString($uuid);
                }
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
