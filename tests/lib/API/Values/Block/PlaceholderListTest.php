<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Block;

use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\API\Values\Block\PlaceholderList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class PlaceholderListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Block\PlaceholderList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                PlaceholderList::class,
                str_replace('\PlaceholderList', '', PlaceholderList::class),
                Placeholder::class,
                stdClass::class
            )
        );

        new PlaceholderList([new Placeholder(), new stdClass(), new Placeholder()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Block\PlaceholderList::__construct
     * @covers \Netgen\Layouts\API\Values\Block\PlaceholderList::getPlaceholders
     */
    public function testGetPlaceholders(): void
    {
        $placeholders = [new Placeholder(), new Placeholder()];

        self::assertSame($placeholders, (new PlaceholderList($placeholders))->getPlaceholders());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Block\PlaceholderList::getPlaceholderIdentifiers
     */
    public function testGetPlaceholderIdentifiers(): void
    {
        $placeholders = [Placeholder::fromArray(['identifier' => 'left']), Placeholder::fromArray(['identifier' => 'right'])];

        self::assertSame(['left', 'right'], (new PlaceholderList($placeholders))->getPlaceholderIdentifiers());
    }
}
