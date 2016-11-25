<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\Templating\Twig;

use Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\TestCase;

class GlobalVariableTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

    public function setUp()
    {
        $this->globalVariable = new GlobalVariable('template.html.twig');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate()
    {
        $this->assertEquals('template.html.twig', $this->globalVariable->getPageLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable::setPageLayoutTemplate
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     */
    public function testSetPageLayoutTemplate()
    {
        $this->globalVariable->setPageLayoutTemplate('new.html.twig');

        $this->assertEquals('new.html.twig', $this->globalVariable->getPageLayoutTemplate());
    }
}
