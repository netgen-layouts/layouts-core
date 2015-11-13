<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension;
use PHPUnit_Framework_TestCase;

class NetgenBlockManagerExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::getName
     */
    public function testGetName()
    {
        $globalHelper = $this->getMock('Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper');
        $extension = new NetgenBlockManagerExtension($globalHelper);

        self::assertEquals('netgen_block_manager', $extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::getGlobals
     */
    public function testGetGlobals()
    {
        $globalHelper = $this->getMock('Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper');
        $extension = new NetgenBlockManagerExtension($globalHelper);

        self::assertEquals(
            array(
                'ngbm' => $globalHelper,
            ),
            $extension->getGlobals()
        );
    }
}
