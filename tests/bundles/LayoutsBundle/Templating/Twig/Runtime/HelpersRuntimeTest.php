<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Item\ValueType\ValueType;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->runtime = new HelpersRuntime(
            $this->layoutServiceMock,
            new ValueTypeRegistry(
                [
                    'value' => ValueType::fromArray(['identifier' => 'value', 'name' => 'Value']),
                ]
            )
        );
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
        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willReturn(Layout::fromArray(['name' => 'Test layout']));

        self::assertSame('Test layout', $this->runtime->getLayoutName($uuid->toString()));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime::getLayoutName
     */
    public function testGetLayoutNameWithNonExistingLayout(): void
    {
        $uuid = Uuid::uuid4();

        $this->layoutServiceMock
            ->expects(self::once())
            ->method('loadLayout')
            ->with(self::equalTo($uuid))
            ->willThrowException(new NotFoundException('layout', $uuid->toString()));

        self::assertSame('', $this->runtime->getLayoutName($uuid->toString()));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime::getValueTypeName
     */
    public function testGetValueTypeName(): void
    {
        $cmsItem = CmsItem::fromArray(['valueType' => 'value']);

        self::assertSame('Value', $this->runtime->getValueTypeName($cmsItem));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\HelpersRuntime::getValueTypeName
     */
    public function testGetValueTypeNameWithNonExistingLayout(): void
    {
        $cmsItem = CmsItem::fromArray(['valueType' => 'non_existing']);

        self::assertSame('', $this->runtime->getValueTypeName($cmsItem));
    }
}
