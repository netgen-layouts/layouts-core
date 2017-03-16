<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Block\Block as CoreBlock;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\TwigBlockDefinition;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\BlockViewProvider;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\View\BlockView\Block;
use Netgen\BlockManager\View\View\BlockViewInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

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
        $block = new CoreBlock(
            array(
                'id' => 42,
                'definition' => new BlockDefinition('block_definition'),
            )
        );

        /** @var \Netgen\BlockManager\View\View\BlockViewInterface $view */
        $view = $this->blockViewProvider->provideView($block);
        $viewBlock = new Block($block, array('definition_param' => 'definition_value'));

        $this->assertInstanceOf(BlockViewInterface::class, $view);

        $this->assertEquals($viewBlock, $view->getBlock());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'block' => $viewBlock,
            ),
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::provideView
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::getTwigBlockContent
     */
    public function testProvideViewWithTwigBlock()
    {
        $block = new CoreBlock(
            array(
                'id' => 42,
                'definition' => new TwigBlockDefinition('block_definition'),
            )
        );

        $twigTemplateMock = $this->createMock(ContextualizedTwigTemplate::class);
        $twigTemplateMock
            ->expects($this->once())
            ->method('renderBlock')
            ->will($this->returnValue('rendered twig block'));

        /** @var \Netgen\BlockManager\View\View\BlockViewInterface $view */
        $view = $this->blockViewProvider->provideView($block, array('twig_template' => $twigTemplateMock));
        $viewBlock = new Block($block, array('definition_param' => 'definition_value'));

        $this->assertInstanceOf(BlockViewInterface::class, $view);

        $this->assertEquals($viewBlock, $view->getBlock());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'block' => $viewBlock,
                'twig_content' => 'rendered twig block',
            ),
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::provideView
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::getTwigBlockContent
     */
    public function testProvideViewWithTwigBlockAndInvalidTwigTemplate()
    {
        $block = new CoreBlock(
            array(
                'id' => 42,
                'definition' => new TwigBlockDefinition('block_definition'),
            )
        );

        $twigTemplate = new stdClass();

        /** @var \Netgen\BlockManager\View\View\BlockViewInterface $view */
        $view = $this->blockViewProvider->provideView($block, array('twig_template' => $twigTemplate));
        $viewBlock = new Block($block, array('definition_param' => 'definition_value'));

        $this->assertInstanceOf(BlockViewInterface::class, $view);

        $this->assertEquals($viewBlock, $view->getBlock());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'block' => $viewBlock,
                'twig_content' => '',
            ),
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::provideView
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::getTwigBlockContent
     */
    public function testProvideViewWithTwigBlockAndNoTwigTemplate()
    {
        $block = new CoreBlock(
            array(
                'id' => 42,
                'definition' => new TwigBlockDefinition('block_definition'),
            )
        );

        /** @var \Netgen\BlockManager\View\View\BlockViewInterface $view */
        $view = $this->blockViewProvider->provideView($block);
        $viewBlock = new Block($block, array('definition_param' => 'definition_value'));

        $this->assertInstanceOf(BlockViewInterface::class, $view);

        $this->assertEquals($viewBlock, $view->getBlock());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'block' => $viewBlock,
                'twig_content' => '',
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
            array(new CoreBlock(), true),
            array(new Layout(), false),
        );
    }
}
