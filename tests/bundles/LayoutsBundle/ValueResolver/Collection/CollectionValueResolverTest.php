<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\CollectionValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class CollectionValueResolverTest extends TestCase
{
    private MockObject $collectionServiceMock;

    private CollectionValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->valueResolver = new CollectionValueResolver($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\CollectionValueResolver::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\CollectionValueResolver::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['collectionId'], $this->valueResolver->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\CollectionValueResolver::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('collection', $this->valueResolver->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\CollectionValueResolver::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Collection::class, $this->valueResolver->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\CollectionValueResolver::loadValue
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
            $this->valueResolver->loadValue(
                [
                    'collectionId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\CollectionValueResolver::loadValue
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
            $this->valueResolver->loadValue(
                [
                    'collectionId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
