<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\SlotValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

#[CoversClass(SlotValueResolver::class)]
final class SlotValueResolverTest extends TestCase
{
    private Stub&CollectionService $collectionServiceStub;

    private SlotValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceStub = self::createStub(CollectionService::class);

        $this->valueResolver = new SlotValueResolver($this->collectionServiceStub);
    }

    public function testResolve(): void
    {
        $uuid = Uuid::v4();
        $slot = Slot::fromArray(['id' => $uuid, 'status' => Status::Draft]);

        $this->collectionServiceStub
            ->method('loadSlotDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($slot);

        $request = Request::create('/');
        $request->attributes->set('slotId', $uuid->toString());

        $argument = new ArgumentMetadata('slot', Slot::class, false, false, null);

        self::assertSame(
            [$slot],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v4();
        $slot = Slot::fromArray(['id' => $uuid, 'status' => Status::Published]);

        $this->collectionServiceStub
            ->method('loadSlot')
            ->with(self::equalTo($uuid))
            ->willReturn($slot);

        $request = Request::create('/');
        $request->attributes->set('slotId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('slot', Slot::class, false, false, null);

        self::assertSame(
            [$slot],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('slot', Slot::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('slotId', '42');

        $argument = new ArgumentMetadata('invalid', Slot::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('slotId', '42');

        $argument = new ArgumentMetadata('slot', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
