<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use Traversable;

final class PlaceholderTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getIdentifier
     */
    public function testDefaultProperties(): void
    {
        $placeholder = new Placeholder();

        $this->assertEquals([], $placeholder->getBlocks());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::count
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getBlocks
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::getIterator
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::offsetExists
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::offsetGet
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::offsetSet
     * @covers \Netgen\BlockManager\Core\Values\Block\Placeholder::offsetUnset
     */
    public function testSetProperties(): void
    {
        $placeholder = new Placeholder(
            [
                'identifier' => 42,
                'blocks' => new ArrayCollection([new Block()]),
            ]
        );

        $this->assertEquals(42, $placeholder->getIdentifier());
        $this->assertEquals([new Block()], $placeholder->getBlocks());

        $this->assertInstanceOf(Traversable::class, $placeholder->getIterator());
        $this->assertEquals([new Block()], iterator_to_array($placeholder->getIterator()));

        $this->assertCount(1, $placeholder);

        $this->assertTrue(isset($placeholder[0]));
        $this->assertEquals(new Block(), $placeholder[0]);

        try {
            $placeholder[1] = new Block();
            $this->fail('Succeeded in setting a new block to placeholder.');
        } catch (RuntimeException $e) {
            // Do nothing
        }

        try {
            unset($placeholder[0]);
            $this->fail('Succeeded in unsetting a block in placeholder.');
        } catch (RuntimeException $e) {
            // Do nothing
        }
    }
}
