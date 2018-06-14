<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Block;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier;
use Netgen\BlockManager\View\View\BlockView;
use PHPUnit\Framework\TestCase;

final class CollectionIdentifierTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new CollectionIdentifier();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $view = new BlockView(
            [
                'block' => new Block(),
                'collection_identifier' => 'default',
            ]
        );

        $this->assertEquals($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
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
     * @covers \Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier::match
     */
    public function testMatchWithNoBlockView(): void
    {
        $this->assertFalse($this->matcher->match(new View(['value' => new Value()]), []));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\CollectionIdentifier::match
     */
    public function testMatchWithNoCollectionIdentifier(): void
    {
        $this->assertFalse($this->matcher->match(new BlockView(['block' => new Block()]), []));
    }
}
