<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\ZoneValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

#[CoversClass(ZoneValueResolver::class)]
final class ZoneValueResolverTest extends TestCase
{
    private Stub&LayoutService $layoutServiceStub;

    private ZoneValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);

        $this->valueResolver = new ZoneValueResolver($this->layoutServiceStub);
    }

    public function testResolve(): void
    {
        $uuid = Uuid::v7();
        $zone = Zone::fromArray(['identifier' => 'left', 'status' => Status::Draft]);
        $layout = Layout::fromArray(['id' => $uuid, 'zones' => ZoneList::fromArray(['left' => $zone])]);

        $this->layoutServiceStub
            ->method('loadLayoutDraft')
            ->willReturn($layout);

        $request = Request::create('/');
        $request->attributes->set('layoutId', $uuid->toString());
        $request->attributes->set('zoneIdentifier', 'left');

        $argument = new ArgumentMetadata('zone', Zone::class, false, false, null);

        self::assertSame(
            [$zone],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v7();
        $zone = Zone::fromArray(['identifier' => 'left', 'status' => Status::Draft]);
        $layout = Layout::fromArray(['id' => $uuid, 'zones' => ZoneList::fromArray(['left' => $zone])]);

        $this->layoutServiceStub
            ->method('loadLayout')
            ->willReturn($layout);

        $request = Request::create('/');
        $request->attributes->set('layoutId', $uuid->toString());
        $request->attributes->set('zoneIdentifier', 'left');
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('zone', Zone::class, false, false, null);

        self::assertSame(
            [$zone],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('zone', Zone::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('layoutId', '42');
        $request->attributes->set('zoneIdentifier', 'left');

        $argument = new ArgumentMetadata('invalid', Zone::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('layoutId', '42');
        $request->attributes->set('zoneIdentifier', 'left');

        $argument = new ArgumentMetadata('zone', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
