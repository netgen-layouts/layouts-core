<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Block;

use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Block\PlaceholderList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PlaceholderList::class)]
final class PlaceholderListTest extends TestCase
{
    public function testGetPlaceholders(): void
    {
        $placeholders = ['one' => new Placeholder(), 'two' => new Placeholder()];

        self::assertSame($placeholders, new PlaceholderList($placeholders)->getPlaceholders());
    }

    public function testGetPlaceholderIdentifiers(): void
    {
        $placeholders = [
            'left' => Placeholder::fromArray(['identifier' => 'left']),
            'right' => Placeholder::fromArray(['identifier' => 'right']),
        ];

        self::assertSame(['left', 'right'], new PlaceholderList($placeholders)->getPlaceholderIdentifiers());
    }
}
