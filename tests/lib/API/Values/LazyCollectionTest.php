<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values;

use Netgen\Layouts\API\Values\LazyCollection;
use PHPUnit\Framework\TestCase;

final class LazyCollectionTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\API\Values\LazyCollection
     */
    private $collection;

    protected function setUp(): void
    {
        $this->collection = new LazyCollection(
            static function (): array {
                return [1, 2, 3];
            }
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LazyCollection::__construct
     * @covers \Netgen\Layouts\API\Values\LazyCollection::doInitialize
     */
    public function testToArray(): void
    {
        self::assertFalse($this->collection->isInitialized());

        self::assertSame([1, 2, 3], $this->collection->toArray());

        self::assertTrue($this->collection->isInitialized());
    }
}
