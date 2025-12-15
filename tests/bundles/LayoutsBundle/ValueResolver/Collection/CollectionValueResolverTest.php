<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\CollectionValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CollectionValueResolver::class)]
final class CollectionValueResolverTest extends TestCase
{
    private Stub&CollectionService $collectionServiceStub;

    private CollectionValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceStub = self::createStub(CollectionService::class);

        $this->valueResolver = new CollectionValueResolver($this->collectionServiceStub);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['collectionId'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('collection', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(Collection::class, $this->valueResolver->getSupportedClass());
    }

    public function testLoadValue(): void
    {
        $collection = new Collection();

        $uuid = Uuid::v4();

        $this->collectionServiceStub
            ->method('loadCollection')
            ->with(self::equalTo($uuid))
            ->willReturn($collection);

        self::assertSame(
            $collection,
            $this->valueResolver->loadValue(
                [
                    'collectionId' => $uuid->toString(),
                    'status' => Status::Published,
                ],
            ),
        );
    }

    public function testLoadValueDraft(): void
    {
        $collection = new Collection();

        $uuid = Uuid::v4();

        $this->collectionServiceStub
            ->method('loadCollectionDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($collection);

        self::assertSame(
            $collection,
            $this->valueResolver->loadValue(
                [
                    'collectionId' => $uuid->toString(),
                    'status' => Status::Draft,
                ],
            ),
        );
    }
}
