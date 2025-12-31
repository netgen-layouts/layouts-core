<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\QueryValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

#[CoversClass(QueryValueResolver::class)]
final class QueryValueResolverTest extends TestCase
{
    private Stub&CollectionService $collectionServiceStub;

    private QueryValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceStub = self::createStub(CollectionService::class);

        $this->valueResolver = new QueryValueResolver($this->collectionServiceStub);
    }

    public function testResolve(): void
    {
        $uuid = Uuid::v7();
        $query = Query::fromArray(['id' => $uuid, 'status' => Status::Draft]);

        $this->collectionServiceStub
            ->method('loadQueryDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($query);

        $request = Request::create('/');
        $request->attributes->set('queryId', $uuid->toString());

        $argument = new ArgumentMetadata('query', Query::class, false, false, null);

        self::assertSame(
            [$query],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolvePublished(): void
    {
        $uuid = Uuid::v7();
        $query = Query::fromArray(['id' => $uuid, 'status' => Status::Published]);

        $this->collectionServiceStub
            ->method('loadQuery')
            ->with(self::equalTo($uuid))
            ->willReturn($query);

        $request = Request::create('/');
        $request->attributes->set('queryId', $uuid->toString());
        $request->attributes->set('_nglayouts_status', Status::Published->value);

        $argument = new ArgumentMetadata('query', Query::class, false, false, null);

        self::assertSame(
            [$query],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSourceName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('invalid', '42');

        $argument = new ArgumentMetadata('query', Query::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidDestinationName(): void
    {
        $request = Request::create('/');
        $request->attributes->set('queryId', '42');

        $argument = new ArgumentMetadata('invalid', Query::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }

    public function testResolveWithInvalidSupportedClass(): void
    {
        $request = Request::create('/');
        $request->attributes->set('queryId', '42');

        $argument = new ArgumentMetadata('query', stdClass::class, false, false, null);

        self::assertSame(
            [],
            [...$this->valueResolver->resolve($request, $argument)],
        );
    }
}
