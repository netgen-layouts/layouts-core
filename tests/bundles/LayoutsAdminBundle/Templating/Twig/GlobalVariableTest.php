<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Templating\Twig;

use Netgen\Bundle\LayoutsAdminBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GlobalVariable::class)]
final class GlobalVariableTest extends TestCase
{
    private GlobalVariable $globalVariable;

    protected function setUp(): void
    {
        $this->globalVariable = new GlobalVariable('default.html.twig');
    }

    public function testGetPageLayoutTemplate(): void
    {
        self::assertSame('default.html.twig', $this->globalVariable->getPageLayoutTemplate());
    }

    public function testSetPageLayoutTemplate(): void
    {
        $this->globalVariable->setPageLayoutTemplate('template.html.twig');

        self::assertSame('template.html.twig', $this->globalVariable->getPageLayoutTemplate());
    }
}
