<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Block\BlockDefinition\Configuration\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Collection::class)]
final class CollectionTest extends TestCase
{
    private Collection $collection;

    protected function setUp(): void
    {
        $this->collection = Collection::fromArray(
            [
                'identifier' => 'collection',
                'validItemTypes' => ['item'],
                'validQueryTypes' => ['query'],
            ],
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('collection', $this->collection->identifier);
    }

    public function testGetValidQueryTypes(): void
    {
        self::assertSame(['query'], $this->collection->validQueryTypes);
    }

    public function testIsValidQueryType(): void
    {
        self::assertTrue($this->collection->isValidQueryType('query'));
        self::assertFalse($this->collection->isValidQueryType('query2'));
    }

    public function testIsValidQueryTypeWithAllValidTypes(): void
    {
        $this->collection = Collection::fromArray(
            [
                'validQueryTypes' => null,
            ],
        );

        self::assertTrue($this->collection->isValidQueryType('query'));
        self::assertTrue($this->collection->isValidQueryType('query2'));
    }

    public function testIsValidQueryTypeWithNoValidTypes(): void
    {
        $this->collection = Collection::fromArray(
            [
                'validQueryTypes' => [],
            ],
        );

        self::assertFalse($this->collection->isValidQueryType('query'));
        self::assertFalse($this->collection->isValidQueryType('query2'));
    }

    public function testGetValidItemTypes(): void
    {
        self::assertSame(['item'], $this->collection->validItemTypes);
    }

    public function testIsValidItemType(): void
    {
        self::assertTrue($this->collection->isValidItemType('item'));
        self::assertFalse($this->collection->isValidItemType('item2'));
    }

    public function testIsValidItemTypeWithAllValidTypes(): void
    {
        $this->collection = Collection::fromArray(
            [
                'validItemTypes' => null,
            ],
        );

        self::assertTrue($this->collection->isValidItemType('item'));
        self::assertTrue($this->collection->isValidItemType('item2'));
    }

    public function testIsValidItemTypeWithNoValidTypes(): void
    {
        $this->collection = Collection::fromArray(
            [
                'validItemTypes' => [],
            ],
        );

        self::assertFalse($this->collection->isValidItemType('item'));
        self::assertFalse($this->collection->isValidItemType('item2'));
    }
}
