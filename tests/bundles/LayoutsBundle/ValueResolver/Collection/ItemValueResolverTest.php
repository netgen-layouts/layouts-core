<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\ItemValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(ItemValueResolver::class)]
final class ItemValueResolverTest extends TestCase
{
    private MockObject&CollectionService $collectionServiceMock;

    private ItemValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->valueResolver = new ItemValueResolver($this->collectionServiceMock);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['itemId'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('item', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(Item::class, $this->valueResolver->getSupportedClass());
    }

    public function testLoadValue(): void
    {
        $item = new Item();

        $uuid = Uuid::uuid4();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadItem')
            ->with(self::equalTo($uuid))
            ->willReturn($item);

        self::assertSame(
            $item,
            $this->valueResolver->loadValue(
                [
                    'itemId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    public function testLoadValueDraft(): void
    {
        $item = new Item();

        $uuid = Uuid::uuid4();

        $this->collectionServiceMock
            ->expects($this->once())
            ->method('loadItemDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($item);

        self::assertSame(
            $item,
            $this->valueResolver->loadValue(
                [
                    'itemId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
