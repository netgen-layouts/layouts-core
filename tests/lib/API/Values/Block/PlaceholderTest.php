<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Block\Placeholder;
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
                'blocks' => BlockList::fromArray([$block]),
            ],
        );

        self::assertSame('placeholder', $placeholder->identifier);

        self::assertCount(1, $placeholder->blocks);
        self::assertSame($block, $placeholder->blocks[0]);
    }
}
