<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\CollectionValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CollectionValueResolver::class)]
final class CollectionValueResolverTest extends TestCase
{
    private Stub&CollectionService $collectionServiceStub;

    private CollectionValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceStub = self::createStub(CollectionService::class);

        $this->valueResolver = new CollectionValueResolver($this->collectionServiceStub);
    }

    public function testResolve(): void
    {
        $uuid = Uuid::v7();
        $collection = Collection::fromArray(['id' => $uuid, 'status' => Status::Draft]);

        $this->collectionServiceStub
            ->method('loadCollectionDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($collection);

        $request = Request::create('/');
        $request->attributes->set('collectionId', $uuid->toString());

        $argument = new ArgumentMetadata('collection', Collection::class, false, false, null);

        self::assertSame(
            [$collection],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v7();
        $collection = Collection::fromArray(['id' => $uuid, 'status' => Status::Published]);

        $this->collectionServiceStub
            ->method('loadCollection')
            ->with(self::equalTo($uuid))
            ->willReturn($collection);

        $request = Request::create('/');
        $request->attributes->set('collectionId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('collection', Collection::class, false, false, null);

        self::assertSame(
            [$collection],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('collection', Collection::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('collectionId', '42');

        $argument = new ArgumentMetadata('invalid', Collection::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('collectionId', '42');

        $argument = new ArgumentMetadata('collection', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
