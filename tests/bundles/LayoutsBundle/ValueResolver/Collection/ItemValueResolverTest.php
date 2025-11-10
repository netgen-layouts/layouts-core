<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\ItemValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class ItemValueResolverTest extends TestCase
{
    private MockObject $collectionServiceMock;

    private ItemValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->valueResolver = new ItemValueResolver($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\ItemValueResolver::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\ItemValueResolver::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['itemId'], $this->valueResolver->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\ItemValueResolver::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('item', $this->valueResolver->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\ItemValueResolver::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Item::class, $this->valueResolver->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\ItemValueResolver::loadValue
     */
    public function testLoadValue(): void
    {
        $item = new Item();

        $uuid = Uuid::uuid4();

        $this->collectionServiceMock
            ->expects(self::once())
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

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\ItemValueResolver::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $item = new Item();

        $uuid = Uuid::uuid4();

        $this->collectionServiceMock
            ->expects(self::once())
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
