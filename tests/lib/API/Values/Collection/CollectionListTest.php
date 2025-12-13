<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CollectionList::class)]
final class CollectionListTest extends TestCase
{
    public function testGetCollections(): void
    {
        $collections = ['one' => new Collection(), 'two' => new Collection()];

        self::assertSame($collections, CollectionList::fromArray($collections)->getCollections());
    }

    public function testGetCollectionIds(): void
    {
        $uuid1 = Uuid::v4();
        $uuid2 = Uuid::v4();

        $collections = [
            'one' => Collection::fromArray(['id' => $uuid1]),
            'two' => Collection::fromArray(['id' => $uuid2]),
        ];

        self::assertSame([$uuid1, $uuid2], CollectionList::fromArray($collections)->getCollectionIds());
    }
}
