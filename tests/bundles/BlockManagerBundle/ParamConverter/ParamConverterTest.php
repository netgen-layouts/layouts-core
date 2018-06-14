<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter;

use Netgen\Bundle\BlockManagerBundle\Tests\Stubs\ParamConverter;
use Netgen\Bundle\BlockManagerBundle\Tests\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Symfony\Component\HttpFoundation\Request;

final class ParamConverterTest extends TestCase
{
    /**
     * @var \Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->paramConverter = new ParamConverter();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApply(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->assertTrue($this->paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(['id' => 42, 'status' => Value::STATUS_DRAFT]),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithLocale(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->attributes->set('locale', 'en');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->assertTrue($this->paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(['id' => 42, 'locale' => 'en', 'status' => Value::STATUS_DRAFT]),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithPublishedRouteStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->attributes->set('_ngbm_status', 'published');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->assertTrue($this->paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(['id' => 42, 'status' => Value::STATUS_PUBLISHED]),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithArchivedRouteStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->attributes->set('_ngbm_status', 'archived');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->assertTrue($this->paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(['id' => 42, 'status' => Value::STATUS_ARCHIVED]),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithDraftRouteStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->attributes->set('_ngbm_status', 'draft');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->assertTrue($this->paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(['id' => 42, 'status' => Value::STATUS_DRAFT]),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithPublishedQueryStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->query->set('published', 'true');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->assertTrue($this->paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(['id' => 42, 'status' => Value::STATUS_PUBLISHED]),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithDraftQueryStatusParam(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->query->set('published', 'false');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->assertTrue($this->paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(['id' => 42, 'status' => Value::STATUS_DRAFT]),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithNoAttribute(): void
    {
        $request = Request::create('/');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertFalse($request->attributes->has('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithEmptyAndOptionalAttribute(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', '');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);
        $configuration->setIsOptional(true);

        $this->assertFalse($this->paramConverter->apply($request, $configuration));
        $this->assertFalse($request->attributes->has('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Required request attribute is empty.
     */
    public function testApplyWithEmptyAndNonOptionalAttribute(): void
    {
        $request = Request::create('/');
        $request->attributes->set('id', '');
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->paramConverter->apply($request, $configuration);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::supports
     */
    public function testSupports(): void
    {
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass(Value::class);

        $this->assertTrue($this->paramConverter->supports($configuration));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::supports
     */
    public function testSupportsInvalidClass(): void
    {
        $configuration = new ParamConverterConfiguration([]);
        $configuration->setClass('Some\Class');

        $this->assertFalse($this->paramConverter->supports($configuration));
    }
}
