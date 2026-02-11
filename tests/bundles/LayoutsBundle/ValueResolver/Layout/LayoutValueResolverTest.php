<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Layout;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Layout\LayoutValueResolver;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

#[CoversClass(LayoutValueResolver::class)]
final class LayoutValueResolverTest extends TestCase
{
    private Stub&LayoutService $layoutServiceStub;

    private LayoutValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);

        $this->valueResolver = new LayoutValueResolver($this->layoutServiceStub);
    }

    public function testResolve(): void
    {
        $uuid = Uuid::v7();
        $layout = Layout::fromArray(['id' => $uuid, 'status' => Status::Draft]);

        $this->layoutServiceStub
            ->method('loadLayoutDraft')
            ->willReturn($layout);

        $request = Request::create('/');
        $request->attributes->set('layoutId', $uuid->toString());

        $argument = new ArgumentMetadata('layout', Layout::class, false, false, null);

        self::assertSame(
            [$layout],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v7();
        $layout = Layout::fromArray(['id' => $uuid, 'status' => Status::Published]);

        $this->layoutServiceStub
            ->method('loadLayout')
            ->willReturn($layout);

        $request = Request::create('/');
        $request->attributes->set('layoutId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('layout', Layout::class, false, false, null);

        self::assertSame(
            [$layout],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveArchived(): void
    {
        $uuid = Uuid::v7();
        $layout = Layout::fromArray(['id' => $uuid, 'status' => Status::Archived]);

        $this->layoutServiceStub
            ->method('loadLayoutArchive')
            ->willReturn($layout);

        $request = Request::create('/');
        $request->attributes->set('layoutId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Archived->value);

        $argument = new ArgumentMetadata('layout', Layout::class, false, false, null);

        self::assertSame(
            [$layout],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('layout', Layout::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('layoutId', '42');

        $argument = new ArgumentMetadata('invalid', Layout::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('layoutId', '42');

        $argument = new ArgumentMetadata('layout', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
