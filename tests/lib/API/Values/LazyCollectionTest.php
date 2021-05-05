<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values;

use Netgen\Layouts\API\Values\LazyCollection;
use PHPUnit\Framework\TestCase;
use stdClass;

final class LazyCollectionTest extends TestCase
{
    /**
     * @var object[]
     */
    private array $values;

    private LazyCollection $collection;

    protected function setUp(): void
    {
        $this->values = [new stdClass(), new stdClass()];

        $this->collection = new LazyCollection(
            fn (): array => $this->values,
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LazyCollection::__construct
     * @covers \Netgen\Layouts\API\Values\LazyCollection::doInitialize
     */
    public function testToArray(): void
    {
        self::assertFalse($this->collection->isInitialized());

        self::assertSame($this->values, $this->collection->toArray());

        self::assertTrue($this->collection->isInitialized());
    }
}
