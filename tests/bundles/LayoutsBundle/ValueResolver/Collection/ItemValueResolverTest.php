<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\ItemValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

#[CoversClass(ItemValueResolver::class)]
final class ItemValueResolverTest extends TestCase
{
    private Stub&CollectionService $collectionServiceStub;

    private ItemValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceStub = self::createStub(CollectionService::class);

        $this->valueResolver = new ItemValueResolver($this->collectionServiceStub);
    }

    public function testResolve(): void
    {
        $uuid = Uuid::v7();
        $item = Item::fromArray(['id' => $uuid, 'status' => Status::Draft]);

        $this->collectionServiceStub
            ->method('loadItemDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($item);

        $request = Request::create('/');
        $request->attributes->set('itemId', $uuid->toString());

        $argument = new ArgumentMetadata('item', Item::class, false, false, null);

        self::assertSame(
            [$item],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v7();
        $item = Item::fromArray(['id' => $uuid, 'status' => Status::Published]);

        $this->collectionServiceStub
            ->method('loadItem')
            ->with(self::equalTo($uuid))
            ->willReturn($item);

        $request = Request::create('/');
        $request->attributes->set('itemId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('item', Item::class, false, false, null);

        self::assertSame(
            [$item],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('item', Item::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('itemId', '42');

        $argument = new ArgumentMetadata('invalid', Item::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('itemId', '42');

        $argument = new ArgumentMetadata('item', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
