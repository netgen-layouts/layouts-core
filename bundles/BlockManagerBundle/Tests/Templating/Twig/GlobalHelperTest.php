<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Netgen\Bundle\BlockManagerBundle\Tests\Stubs\Configuration;
use PHPUnit_Framework_TestCase;

class GlobalHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getConfiguration
     */
    public function testGetConfiguration()
    {
        $configuration = new Configuration();
        $globalHelper = new GlobalHelper($configuration);

        self::assertEquals($configuration, $globalHelper->getConfiguration());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutView
     */
    public function testGetDefaultLayoutView()
    {
        $globalHelper = new GlobalHelper(new Configuration());

        self::assertNull($globalHelper->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::setLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutView
     */
    public function testGetLayoutView()
    {
        $layoutView = new LayoutView();

        $globalHelper = new GlobalHelper(new Configuration());
        $globalHelper->setLayoutView($layoutView);

        self::assertEquals($layoutView, $globalHelper->getLayoutView());
    }
}
