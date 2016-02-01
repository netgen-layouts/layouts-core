<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Netgen\BlockManager\Configuration\ConfigurationInterface;

class GlobalHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getConfiguration
     */
    public function testGetConfiguration()
    {
        $configuration = $this->getMock(ConfigurationInterface::class);
        $globalHelper = new GlobalHelper($configuration);

        self::assertEquals($configuration, $globalHelper->getConfiguration());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutView
     */
    public function testGetDefaultLayoutView()
    {
        $globalHelper = new GlobalHelper(
            $this->getMock(ConfigurationInterface::class)
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
            $this->getMock(ConfigurationInterface::class)
        );

        $layoutView = new LayoutView();
        $globalHelper->setLayoutView($layoutView);

        self::assertEquals($layoutView, $globalHelper->getLayoutView());
    }
}
