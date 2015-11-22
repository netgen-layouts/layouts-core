<?php

namespace Netgen\BlockManager\Tests\View\TemplateResolver;

use Netgen\BlockManager\View\TemplateResolver\LayoutViewTemplateResolver;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\View\BlockView;

class LayoutViewTemplateResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\TemplateResolver\LayoutViewTemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver\LayoutViewTemplateResolver::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($view, $supports)
    {
        $configuration = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configuration
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->equalTo('layout_view'))
            ->will($this->returnValue(array()));

        $layoutViewTemplateResolver = new LayoutViewTemplateResolver(
            array(),
            $configuration
        );

        self::assertEquals($supports, $layoutViewTemplateResolver->supports($view));
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
            array(new BlockView(), false),
            array(new LayoutView(), true),
        );
    }
}
