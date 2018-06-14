<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\BlockViewProvider;
use Netgen\BlockManager\View\View\BlockViewInterface;
use PHPUnit\Framework\TestCase;

final class BlockViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $blockViewProvider;

    public function setUp(): void
    {
        $this->blockViewProvider = new BlockViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $block = new Block(
            [
                'id' => 42,
                'definition' => new BlockDefinition(),
                'configs' => [
                    'http_cache' => new Config(
                        [
                            'parameters' => [
                                'use_http_cache' => new Parameter(['value' => true]),
                                'shared_max_age' => new Parameter(['value' => 400]),
                            ],
                        ]
                    ),
                ],
            ]
        );

        /** @var \Netgen\BlockManager\View\View\BlockViewInterface $view */
        $view = $this->blockViewProvider->provideView($block);

        $this->assertInstanceOf(BlockViewInterface::class, $view);

        $this->assertEquals($block, $view->getBlock());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            [
                'block' => $block,
            ],
            $view->getParameters()
        );

        $this->assertTrue($view->isCacheable());
        $this->assertEquals(400, $view->getSharedMaxAge());
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        $this->assertEquals($supports, $this->blockViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Value(), false],
            [new Block(), true],
            [new Layout(), false],
        ];
    }
}
