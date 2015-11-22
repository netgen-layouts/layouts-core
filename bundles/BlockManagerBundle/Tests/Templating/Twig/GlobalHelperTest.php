<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;

class GlobalHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getConfiguration
     */
    public function testGetConfiguration()
    {
        $configuration = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $globalHelper = new GlobalHelper($configuration);

        self::assertEquals($configuration, $globalHelper->getConfiguration());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutView
     */
    public function testGetDefaultLayoutView()
    {
        $globalHelper = new GlobalHelper(
            $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface')
        );

        self::assertNull($globalHelper->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::setLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutView
     */
    public function testGetLayoutView()
    {
        $globalHelper = new GlobalHelper(
            $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface')
        );

        $layoutView = new LayoutView();
        $globalHelper->setLayoutView($layoutView);

        self::assertEquals($layoutView, $globalHelper->getLayoutView());
    }
}
