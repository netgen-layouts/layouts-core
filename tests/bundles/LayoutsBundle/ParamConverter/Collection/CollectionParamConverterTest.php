<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\CollectionParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use PHPUnit\Framework\TestCase;

final class CollectionParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $collectionServiceMock;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\CollectionParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new CollectionParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\CollectionParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\CollectionParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['collectionId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\CollectionParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('collection', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\CollectionParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Collection::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\CollectionParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $collection = new Collection();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadCollection')
            ->with(self::identicalTo(42))
            ->willReturn($collection);

        self::assertSame(
            $collection,
            $this->paramConverter->loadValue(
                [
                    'collectionId' => 42,
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\CollectionParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $collection = new Collection();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadCollectionDraft')
            ->with(self::identicalTo(42))
            ->willReturn($collection);

        self::assertSame(
            $collection,
            $this->paramConverter->loadValue(
                [
                    'collectionId' => 42,
                    'status' => 'draft',
                ]
            )
        );
    }
}
