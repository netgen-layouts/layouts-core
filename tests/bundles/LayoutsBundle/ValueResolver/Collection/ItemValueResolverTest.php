<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\ItemValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(ItemValueResolver::class)]
final class ItemValueResolverTest extends TestCase
{
    private Stub&CollectionService $collectionServiceStub;

    private ItemValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceStub = self::createStub(CollectionService::class);

        $this->valueResolver = new ItemValueResolver($this->collectionServiceStub);
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

        $uuid = Uuid::v4();

        $this->collectionServiceStub
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

        $uuid = Uuid::v4();

        $this->collectionServiceStub
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
