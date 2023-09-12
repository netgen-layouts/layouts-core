<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\Block\CollectionIdentifier;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\TestCase;

final class CollectionIdentifierTest extends TestCase
{
    private CollectionIdentifier $matcher;

    protected function setUp(): void
    {
        $this->matcher = new CollectionIdentifier();
    }

    /**
     * @param mixed[] $config
     *
     * @covers \Netgen\Layouts\View\Matcher\Block\CollectionIdentifier::match
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $view = new BlockView(new Block());

        $view->addParameter('collection_identifier', 'default');

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['featured'], false],
            [['default'], true],
            [['featured', 'featured2'], false],
            [['featured', 'default'], true],
        ];
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Block\CollectionIdentifier::match
     */
    public function testMatchWithNoBlockView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\Block\CollectionIdentifier::match
     */
    public function testMatchWithNoCollectionIdentifier(): void
    {
        self::assertFalse($this->matcher->match(new BlockView(new Block()), []));
    }
}
