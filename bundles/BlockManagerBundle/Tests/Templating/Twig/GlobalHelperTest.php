<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\BlockManager\View\LayoutView;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use PHPUnit_Framework_TestCase;

class GlobalHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getPagelayout
     */
    public function testGetDefaultPagelayout()
    {
        $globalHelper = new GlobalHelper();

        self::assertNull($globalHelper->getPagelayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::setPagelayout
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getPagelayout
     */
    public function testGetPagelayout()
    {
        $globalHelper = new GlobalHelper();
        $globalHelper->setPagelayout('pagelayout.html.twig');

        self::assertEquals('pagelayout.html.twig', $globalHelper->getPagelayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutView
     */
    public function testGetDefaultLayoutView()
    {
        $globalHelper = new GlobalHelper();

        self::assertNull($globalHelper->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::setLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper::getLayoutView
     */
    public function testGetLayoutView()
    {
        $layoutView = new LayoutView();

        $globalHelper = new GlobalHelper();
        $globalHelper->setLayoutView($layoutView);

        self::assertEquals($layoutView, $globalHelper->getLayoutView());
    }
}
