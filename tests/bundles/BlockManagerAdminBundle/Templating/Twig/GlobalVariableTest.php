<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\Templating\Twig;

use Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\TestCase;

final class GlobalVariableTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable
     */
    private $globalVariable;

    public function setUp(): void
    {
        $this->globalVariable = new GlobalVariable('default.html.twig');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate(): void
    {
        $this->assertSame('default.html.twig', $this->globalVariable->getPageLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable::setPageLayoutTemplate
     */
    public function testSetPageLayoutTemplate(): void
    {
        $this->globalVariable->setPageLayoutTemplate('template.html.twig');

        $this->assertSame('template.html.twig', $this->globalVariable->getPageLayoutTemplate());
    }
}
