<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\EntityLoader;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Transfer\Output\EntityLoader\LayoutEntityLoader;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class LayoutEntityLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \Netgen\Layouts\Transfer\Output\EntityLoader\LayoutEntityLoader
     */
    private $entityLoader;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->entityLoader = new LayoutEntityLoader(
            $this->layoutServiceMock
        );
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\EntityLoader\LayoutEntityLoader::__construct
     * @covers \Netgen\Layouts\Transfer\Output\EntityLoader\LayoutEntityLoader::loadEntities
     */
    public function testLoadEntities(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $layout1 = Layout::fromArray(['id' => $uuid1]);
        $layout2 = Layout::fromArray(['id' => $uuid2]);

        $this->layoutServiceMock
            ->method('loadLayout')
            ->withConsecutive(
                [self::equalTo($uuid1)],
                [self::equalTo($uuid2)]
            )
            ->willReturnOnConsecutiveCalls($layout1, $layout2);

        $entities = [];
        foreach ($this->entityLoader->loadEntities([$uuid1->toString(), $uuid2->toString()]) as $entity) {
            $entities[] = $entity;
        }

        self::assertSame([$layout1, $layout2], $entities);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\EntityLoader\LayoutEntityLoader::__construct
     * @covers \Netgen\Layouts\Transfer\Output\EntityLoader\LayoutEntityLoader::loadEntities
     */
    public function testLoadEntitiesWithNonExistentEntity(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $layout = Layout::fromArray(['id' => $uuid2]);

        $this->layoutServiceMock
            ->method('loadLayout')
            ->withConsecutive(
                [self::equalTo($uuid1)],
                [self::equalTo($uuid2)]
            )
            ->willReturnOnConsecutiveCalls(
                self::throwException(new NotFoundException('layout', $uuid1->toString())),
                self::returnValue($layout)
            );

        $entities = [];
        foreach ($this->entityLoader->loadEntities([$uuid1->toString(), $uuid2->toString()]) as $entity) {
            $entities[] = $entity;
        }

        self::assertSame([$layout], $entities);
    }
}
