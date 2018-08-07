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

        self::assertSame([], $placeholder->getBlocks());
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
        $block = new Block();

        $placeholder = Placeholder::fromArray(
            [
                'identifier' => 'placeholder',
                'blocks' => new ArrayCollection([$block]),
            ]
        );

        self::assertSame('placeholder', $placeholder->getIdentifier());
        self::assertSame([$block], $placeholder->getBlocks());

        self::assertInstanceOf(Traversable::class, $placeholder->getIterator());
        self::assertSame([$block], iterator_to_array($placeholder->getIterator()));

        self::assertCount(1, $placeholder);

        self::assertTrue(isset($placeholder[0]));
        self::assertSame($block, $placeholder[0]);

        try {
            $placeholder[1] = $block;
            self::fail('Succeeded in setting a new block to placeholder.');
        } catch (RuntimeException $e) {
            // Do nothing
        }

        try {
            unset($placeholder[0]);
            self::fail('Succeeded in unsetting a block in placeholder.');
        } catch (RuntimeException $e) {
            // Do nothing
        }
    }
}
