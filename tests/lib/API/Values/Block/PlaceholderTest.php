<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Placeholder::class)]
final class PlaceholderTest extends TestCase
{
    public function testSetProperties(): void
    {
        $block = new Block();

        $placeholder = Placeholder::fromArray(
            [
                'identifier' => 'placeholder',
                'blocks' => new ArrayCollection([$block]),
            ],
        );

        self::assertSame('placeholder', $placeholder->getIdentifier());

        self::assertCount(1, $placeholder->getBlocks());
        self::assertSame($block, $placeholder->getBlocks()[0]);

        self::assertSame([$block], [...$placeholder]);

        self::assertCount(1, $placeholder);

        self::assertTrue(isset($placeholder[0]));
        self::assertSame($block, $placeholder[0]);
    }

    public function testSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $placeholder = Placeholder::fromArray(
            [
                'blocks' => new ArrayCollection([new Block()]),
            ],
        );

        $placeholder[1] = new Block();
    }

    public function testUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $placeholder = Placeholder::fromArray(
            [
                'blocks' => new ArrayCollection([new Block()]),
            ],
        );

        unset($placeholder[0]);
    }
}
