<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Templating\Twig;

use Netgen\Bundle\LayoutsAdminBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\TestCase;

final class GlobalVariableTest extends TestCase
{
    private GlobalVariable $globalVariable;

    protected function setUp(): void
    {
        $this->globalVariable = new GlobalVariable('default.html.twig');
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Templating\Twig\GlobalVariable::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate(): void
    {
        self::assertSame('default.html.twig', $this->globalVariable->getPageLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Templating\Twig\GlobalVariable::setPageLayoutTemplate
     */
    public function testSetPageLayoutTemplate(): void
    {
        $this->globalVariable->setPageLayoutTemplate('template.html.twig');

        self::assertSame('template.html.twig', $this->globalVariable->getPageLayoutTemplate());
    }
}
