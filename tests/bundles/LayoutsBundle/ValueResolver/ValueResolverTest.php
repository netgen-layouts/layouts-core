<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver;

use Netgen\Bundle\LayoutsBundle\Tests\Stubs\Value;
use Netgen\Bundle\LayoutsBundle\Tests\Stubs\ValueResolver as ValueResolverStub;
use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[CoversClass(ValueResolver::class)]
final class ValueResolverTest extends TestCase
{
    private ValueResolverStub $valueResolver;

    protected function setUp(): void
    {
        $this->valueResolver = new ValueResolverStub();
    }

    public function testResolve(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $argument = new ArgumentMetadata('value', Value::class, false, false, null);

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertArrayHasKey(0, $values);
        self::assertInstanceOf(Value::class, $values[0]);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $values[0]->id->toString());
        self::assertSame(Status::Draft, $values[0]->status);
    }

    public function testResolveWithLocale(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->attributes->set('locale', 'en');
        $argument = new ArgumentMetadata('value', Value::class, false, false, null);

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertArrayHasKey(0, $values);
        self::assertInstanceOf(Value::class, $values[0]);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $values[0]->id->toString());
        self::assertSame('en', $values[0]->locale);
        self::assertSame(Status::Draft, $values[0]->status);
    }

    public function testResolveWithPublishedRouteStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->attributes->set('_nglayouts_status', 'published');
        $argument = new ArgumentMetadata('value', Value::class, false, false, null);

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertArrayHasKey(0, $values);
        self::assertInstanceOf(Value::class, $values[0]);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $values[0]->id->toString());
        self::assertSame(Status::Published, $values[0]->status);
    }

    public function testResolveWithArchivedRouteStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->attributes->set('_nglayouts_status', 'archived');
        $argument = new ArgumentMetadata('value', Value::class, false, false, null);

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertArrayHasKey(0, $values);
        self::assertInstanceOf(Value::class, $values[0]);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $values[0]->id->toString());
        self::assertSame(Status::Archived, $values[0]->status);
    }

    public function testResolveWithDraftRouteStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->attributes->set('_nglayouts_status', 'draft');
        $argument = new ArgumentMetadata('value', Value::class, false, false, null);

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertArrayHasKey(0, $values);
        self::assertInstanceOf(Value::class, $values[0]);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $values[0]->id->toString());
        self::assertSame(Status::Draft, $values[0]->status);
    }

    public function testResolveWithPublishedQueryStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->query->set('published', 'true');
        $argument = new ArgumentMetadata('value', Value::class, false, false, null);

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertArrayHasKey(0, $values);
        self::assertInstanceOf(Value::class, $values[0]);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $values[0]->id->toString());
        self::assertSame(Status::Published, $values[0]->status);
    }

    public function testResolveWithDraftQueryStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->query->set('published', 'false');
        $argument = new ArgumentMetadata('value', Value::class, false, false, null);

        $values = [...$this->valueResolver->resolve($request, $argument)];

        self::assertArrayHasKey(0, $values);
        self::assertInstanceOf(Value::class, $values[0]);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $values[0]->id->toString());
        self::assertSame(Status::Draft, $values[0]->status);
    }

    public function testResolveWithNoAttribute(): void
    {
        $request = Request::create('/');
        $argument = new ArgumentMetadata('value', Value::class, false, false, null);

        self::assertSame([], [...$this->valueResolver->resolve($request, $argument)]);
    }

    public function testResolveWithEmptyAndNonOptionalAttribute(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required request attribute is empty.');

        $request = Request::create('/');
        $request->attributes->set('id', '');
        $argument = new ArgumentMetadata('value', Value::class, false, false, null);

        self::assertSame([], [...$this->valueResolver->resolve($request, $argument)]);
    }
}
