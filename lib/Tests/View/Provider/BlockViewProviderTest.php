<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\TwigBlockDefinition;
use Netgen\BlockManager\View\Provider\BlockViewProvider;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View\BlockView\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\View\BlockViewInterface;
use PHPUnit\Framework\TestCase;
use Twig_Template;

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
                'blockDefinition' => new BlockDefinition('block_definition'),
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
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::provideView
     */
    public function testProvideViewWithTwigBlock()
    {
        $block = new Block(
            array(
                'id' => 42,
                'blockDefinition' => new TwigBlockDefinition('block_definition'),
                'parameters' => array(
                    'block_name' => new ParameterValue(
                        array(
                            'value' => 'twig_block',
                        )
                    ),
                ),
            )
        );

        $templateMock = $this->createMock(Twig_Template::class);
        $templateMock
            ->expects($this->any())
            ->method('displayBlock')
            ->with($this->equalTo('twig_block'))
            ->will($this->returnCallback(
                function ($blockName) {
                    echo 'rendered';
                }
            )
        );

        /** @var \Netgen\BlockManager\View\View\BlockViewInterface $view */
        $view = $this->blockViewProvider->provideView(
            $block,
            array(
                'twigTemplate' => new ContextualizedTwigTemplate($templateMock),
            )
        );

        $this->assertInstanceOf(BlockViewInterface::class, $view);

        $this->assertEquals($block, $view->getBlock());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'block' => $block,
                'twig_block_content' => 'rendered',
            ),
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::provideView
     */
    public function testProvideViewWithInvalidTwigTemplate()
    {
        $block = new Block(
            array(
                'id' => 42,
                'blockDefinition' => new TwigBlockDefinition('block_definition'),
                'parameters' => array(
                    'block_name' => new ParameterValue(
                        array(
                            'value' => 'twig_block',
                        )
                    ),
                ),
            )
        );

        /** @var \Netgen\BlockManager\View\View\BlockViewInterface $view */
        $view = $this->blockViewProvider->provideView(
            $block,
            array(
                'twigTemplate' => 'template',
            )
        );

        $this->assertInstanceOf(BlockViewInterface::class, $view);

        $this->assertEquals($block, $view->getBlock());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'block' => $block,
                'twig_block_content' => '',
            ),
            $view->getParameters()
        );
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
