<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;

class NetgenBlockManagerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalHelperMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->globalHelperMock = $this->getMockBuilder(GlobalHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->extension = new NetgenBlockManagerExtension($this->globalHelperMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::getName
     */
    public function testGetName()
    {
        self::assertEquals('netgen_block_manager', $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\NetgenBlockManagerExtension::getGlobals
     */
    public function testGetGlobals()
    {
        self::assertEquals(
            array(
                'ngbm' => $this->globalHelperMock,
            ),
            $this->extension->getGlobals()
        );
    }
}
