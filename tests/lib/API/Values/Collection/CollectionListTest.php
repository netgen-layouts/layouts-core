<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class CollectionListTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\CollectionList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                CollectionList::class,
                str_replace('\CollectionList', '', CollectionList::class),
                Collection::class,
                stdClass::class
            )
        );

        new CollectionList([new Collection(), new stdClass(), new Collection()]);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\CollectionList::__construct
     * @covers \Netgen\BlockManager\API\Values\Collection\CollectionList::getCollections
     */
    public function testGetCollections(): void
    {
        $collections = [new Collection(), new Collection()];

        self::assertSame($collections, (new CollectionList($collections))->getCollections());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Collection\CollectionList::getCollectionIds
     */
    public function testGetCollectionIds(): void
    {
        $collections = [Collection::fromArray(['id' => 42]), Collection::fromArray(['id' => 24])];

        self::assertSame([42, 24], (new CollectionList($collections))->getCollectionIds());
    }
}
