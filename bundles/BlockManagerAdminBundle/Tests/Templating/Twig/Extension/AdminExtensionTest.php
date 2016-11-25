<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\Templating\Twig;

use Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\Extension\AdminExtension;
use Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\TestCase;

class AdminExtensionTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $globalVariableMock;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\Extension\AdminExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->globalVariableMock = $this->createMock(GlobalVariable::class);

        $this->extension = new AdminExtension($this->globalVariableMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\Extension\AdminExtension::getName
     */
    public function testGetName()
    {
        $this->assertEquals(get_class($this->extension), $this->extension->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\Extension\AdminExtension::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\Extension\AdminExtension::getGlobals
     */
    public function testGetGlobals()
    {
        $this->assertEquals(
            array(
                'ngbm_admin' => $this->globalVariableMock,
            ),
            $this->extension->getGlobals()
        );
    }
}
