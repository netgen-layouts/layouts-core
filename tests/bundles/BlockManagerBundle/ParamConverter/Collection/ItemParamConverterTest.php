<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Collection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter;
use PHPUnit\Framework\TestCase;

final class ItemParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new ItemParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['itemId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('item', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Item::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $item = new Item();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadItem')
            ->with(self::identicalTo(42))
            ->willReturn($item);

        self::assertSame(
            $item,
            $this->paramConverter->loadValue(
                [
                    'itemId' => 42,
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection\ItemParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $item = new Item();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadItemDraft')
            ->with(self::identicalTo(42))
            ->willReturn($item);

        self::assertSame(
            $item,
            $this->paramConverter->loadValue(
                [
                    'itemId' => 42,
                    'status' => 'draft',
                ]
            )
        );
    }
}
