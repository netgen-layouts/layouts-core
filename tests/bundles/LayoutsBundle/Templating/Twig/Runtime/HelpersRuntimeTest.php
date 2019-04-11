<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime;
use PHPUnit\Framework\TestCase;

final class HelpersRuntimeTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime
     */
    private $runtime;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    public function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->runtime = new HelpersRuntime($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime::getLocaleName
     */
    public function testGetLocaleName(): void
    {
        $localeName = $this->runtime->getLocaleName('hr_HR', 'hr_HR');

        self::assertSame('hrvatski (Hrvatska)', $localeName);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime::getLayoutName
     */
    public function testGetLayoutName(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo(42))
            ->willReturn(Layout::fromArray(['name' => 'Test layout']));

        self::assertSame('Test layout', $this->runtime->getLayoutName(42));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime::getLayoutName
     */
    public function testGetLayoutNameWithNonExistingLayout(): void
    {
        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo(42))
            ->willThrowException(new NotFoundException('layout', 42));

        self::assertSame('', $this->runtime->getLayoutName(42));
    }
}
