<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\BlockViewProvider;
use Netgen\BlockManager\View\View\BlockViewInterface;
use PHPUnit\Framework\TestCase;

class BlockViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $blockViewProvider;

    public function setUp()
    {
        $this->blockViewProvider = new BlockViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::provideView
     */
    public function testProvideView()
    {
        $block = new Block(
            array(
                'id' => 42,
                'configs' => array(
                    'http_cache' => new Config(
                        array(
                            'parameters' => array(
                                'use_http_cache' => new ParameterValue(array('value' => true)),
                                'shared_max_age' => new ParameterValue(array('value' => 400)),
                            ),
                        )
                    ),
                ),
                'definition' => new BlockDefinition('block_definition'),
            )
        );

        /** @var \Netgen\BlockManager\View\View\BlockViewInterface $view */
        $view = $this->blockViewProvider->provideView($block);

        $this->assertInstanceOf(BlockViewInterface::class, $view);

        $this->assertEquals($block, $view->getBlock());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'block' => $block,
            ),
            $view->getParameters()
        );

        $this->assertTrue($view->isCacheable());
        $this->assertEquals(400, $view->getSharedMaxAge());
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        $this->assertEquals($supports, $this->blockViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Value(), false),
            array(new Block(), true),
            array(new Layout(), false),
        );
    }
}
