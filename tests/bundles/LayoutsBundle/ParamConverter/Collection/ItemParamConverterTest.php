<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\ItemParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class ItemParamConverterTest extends TestCase
{
    private MockObject $collectionServiceMock;

    private ItemParamConverter $paramConverter;

    protected function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new ItemParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\ItemParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\ItemParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['itemId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\ItemParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('item', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\ItemParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Item::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\ItemParamConverter::loadValue
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
            $this->paramConverter->loadValue(
                [
                    'itemId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\ItemParamConverter::loadValue
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
            $this->paramConverter->loadValue(
                [
                    'itemId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
