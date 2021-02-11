<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter;

use Netgen\Bundle\LayoutsBundle\Tests\Stubs\ParamConverter;
use Netgen\Bundle\LayoutsBundle\Tests\Stubs\Value;
use Netgen\Layouts\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Symfony\Component\HttpFoundation\Request;

final class ParamConverterTest extends TestCase
{
    private ParamConverter $paramConverter;

    protected function setUp(): void
    {
        $this->paramConverter = new ParamConverter();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::apply
     */
    public function testApply(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        self::assertTrue($this->paramConverter->apply($request, $configuration));
        self::assertTrue($request->attributes->has('value'));

        $value = $request->attributes->get('value');

        self::assertInstanceOf(Value::class, $value);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $value->getId()->toString());
        self::assertSame(Value::STATUS_DRAFT, $value->getStatus());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithLocale(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->attributes->set('locale', 'en');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        self::assertTrue($this->paramConverter->apply($request, $configuration));
        self::assertTrue($request->attributes->has('value'));

        $value = $request->attributes->get('value');

        self::assertInstanceOf(Value::class, $value);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $value->getId()->toString());
        self::assertSame('en', $value->getLocale());
        self::assertSame(Value::STATUS_DRAFT, $value->getStatus());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithPublishedRouteStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->attributes->set('_nglayouts_status', 'published');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        self::assertTrue($this->paramConverter->apply($request, $configuration));
        self::assertTrue($request->attributes->has('value'));

        $value = $request->attributes->get('value');

        self::assertInstanceOf(Value::class, $value);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $value->getId()->toString());
        self::assertSame(Value::STATUS_PUBLISHED, $value->getStatus());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithArchivedRouteStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->attributes->set('_nglayouts_status', 'archived');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        self::assertTrue($this->paramConverter->apply($request, $configuration));
        self::assertTrue($request->attributes->has('value'));

        $value = $request->attributes->get('value');

        self::assertInstanceOf(Value::class, $value);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $value->getId()->toString());
        self::assertSame(Value::STATUS_ARCHIVED, $value->getStatus());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithDraftRouteStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->attributes->set('_nglayouts_status', 'draft');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        self::assertTrue($this->paramConverter->apply($request, $configuration));
        self::assertTrue($request->attributes->has('value'));

        $value = $request->attributes->get('value');

        self::assertInstanceOf(Value::class, $value);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $value->getId()->toString());
        self::assertSame(Value::STATUS_DRAFT, $value->getStatus());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithPublishedQueryStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->query->set('published', 'true');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        self::assertTrue($this->paramConverter->apply($request, $configuration));
        self::assertTrue($request->attributes->has('value'));

        $value = $request->attributes->get('value');

        self::assertInstanceOf(Value::class, $value);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $value->getId()->toString());
        self::assertSame(Value::STATUS_PUBLISHED, $value->getStatus());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithDraftQueryStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 'f06f245a-f951-52c8-bfa3-84c80154eadc');
        $request->query->set('published', 'false');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        self::assertTrue($this->paramConverter->apply($request, $configuration));
        self::assertTrue($request->attributes->has('value'));

        $value = $request->attributes->get('value');

        self::assertInstanceOf(Value::class, $value);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $value->getId()->toString());
        self::assertSame(Value::STATUS_DRAFT, $value->getStatus());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithNoAttribute(): void
    {
        $request = Request::create('/');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertFalse($request->attributes->has('value'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithEmptyAndOptionalAttribute(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', '');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);
        $configuration->setIsOptional(true);

        self::assertFalse($this->paramConverter->apply($request, $configuration));
        self::assertFalse($request->attributes->has('value'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithEmptyAndNonOptionalAttribute(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required request attribute is empty.');

        $request = Request::create('/');
        $request->attributes->set('id', '');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->paramConverter->apply($request, $configuration);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::supports
     */
    public function testSupports(): void
    {
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        self::assertTrue($this->paramConverter->supports($configuration));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter::supports
     */
    public function testSupportsInvalidClass(): void
    {
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass('Some\Class');

        self::assertFalse($this->paramConverter->supports($configuration));
    }
}
