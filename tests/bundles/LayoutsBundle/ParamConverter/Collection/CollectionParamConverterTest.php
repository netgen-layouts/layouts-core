<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\CollectionParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class CollectionParamConverterTest extends TestCase
{
    private MockObject $collectionServiceMock;

    private CollectionParamConverter $paramConverter;

    protected function setUp(): void
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

        $uuid = Uuid::uuid4();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadCollection')
            ->with(self::equalTo($uuid))
            ->willReturn($collection);

        self::assertSame(
            $collection,
            $this->paramConverter->loadValue(
                [
                    'collectionId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\CollectionParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $collection = new Collection();

        $uuid = Uuid::uuid4();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadCollectionDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($collection);

        self::assertSame(
            $collection,
            $this->paramConverter->loadValue(
                [
                    'collectionId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
