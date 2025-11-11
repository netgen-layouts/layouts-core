<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use TypeError;

use function sprintf;
use function str_replace;

#[CoversClass(CollectionList::class)]
final class CollectionListTest extends TestCase
{
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageMatches(
            sprintf(
                '/(must be an instance of|must be of type) %s, (instance of )?%s given/',
                str_replace('\\', '\\\\', Collection::class),
                stdClass::class,
            ),
        );

        new CollectionList(['one' => new Collection(), 'two' => new stdClass(), 'three' => new Collection()]);
    }

    public function testGetCollections(): void
    {
        $collections = ['one' => new Collection(), 'two' => new Collection()];

        self::assertSame($collections, new CollectionList($collections)->getCollections());
    }

    public function testGetCollectionIds(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $collections = [
            'one' => Collection::fromArray(['id' => $uuid1]),
            'two' => Collection::fromArray(['id' => $uuid2]),
        ];

        self::assertSame([$uuid1, $uuid2], new CollectionList($collections)->getCollectionIds());
    }
}
