<?php

namespace Netgen\BlockManager\View\Tests\TemplateResolver;

use Netgen\BlockManager\View\TemplateResolver\BlockViewTemplateResolver;
use Netgen\BlockManager\View\Tests\Stubs\View;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\View\BlockView;

class BlockViewTemplateResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\TemplateResolver\BlockViewTemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver\BlockViewTemplateResolver::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($view, $supports)
    {
        $configuration = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configuration
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->equalTo('block_view'))
            ->will($this->returnValue(array()));

        $blockViewTemplateResolver = new BlockViewTemplateResolver(
            array(),
            $configuration
        );

        self::assertEquals($supports, $blockViewTemplateResolver->supports($view));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new View(), false),
            array(new BlockView(), true),
            array(new LayoutView(), false),
        );
    }
}
