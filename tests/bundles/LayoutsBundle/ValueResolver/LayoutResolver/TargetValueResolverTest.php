<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ValueResolver\LayoutResolver\TargetValueResolver;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

#[CoversClass(TargetValueResolver::class)]
final class TargetValueResolverTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private TargetValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->valueResolver = new TargetValueResolver($this->layoutResolverServiceStub);
    }

    public function testResolve(): void
    {
        $uuid = Uuid::v7();
        $target = Target::fromArray(['id' => $uuid, 'status' => Status::Draft]);

        $this->layoutResolverServiceStub
            ->method('loadTargetDraft')
            ->willReturn($target);

        $request = Request::create('/');
        $request->attributes->set('targetId', $uuid->toString());

        $argument = new ArgumentMetadata('target', Target::class, false, false, null);

        self::assertSame(
            [$target],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v7();
        $target = Target::fromArray(['id' => $uuid, 'status' => Status::Published]);

        $this->layoutResolverServiceStub
            ->method('loadTarget')
            ->willReturn($target);

        $request = Request::create('/');
        $request->attributes->set('targetId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('target', Target::class, false, false, null);

        self::assertSame(
            [$target],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('target', Target::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('targetId', '42');

        $argument = new ArgumentMetadata('invalid', Target::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('targetId', '42');

        $argument = new ArgumentMetadata('target', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
