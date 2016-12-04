<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter;

use Netgen\Bundle\BlockManagerBundle\Tests\Stubs\ParamConverter;
use Netgen\Bundle\BlockManagerBundle\Tests\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Symfony\Component\HttpFoundation\Request;

class ParamConverterTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApply()
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass(Value::class);

        $paramConverter = new ParamConverter();
        $this->assertTrue($paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(array('id' => 42, 'published' => false)),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithPublishedRouteStatusParam()
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->attributes->set(ParamConverter::ROUTE_STATUS_PARAM, 'published');
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass(Value::class);

        $paramConverter = new ParamConverter();
        $this->assertTrue($paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(array('id' => 42, 'published' => true)),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithDraftRouteStatusParam()
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->attributes->set(ParamConverter::ROUTE_STATUS_PARAM, 'draft');
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass(Value::class);

        $paramConverter = new ParamConverter();
        $this->assertTrue($paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(array('id' => 42, 'published' => false)),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithPublishedQueryStatusParam()
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->query->set('published', 'true');
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass(Value::class);

        $paramConverter = new ParamConverter();
        $this->assertTrue($paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(array('id' => 42, 'published' => true)),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithDraftQueryStatusParam()
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->query->set('published', 'false');
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass(Value::class);

        $paramConverter = new ParamConverter();
        $this->assertTrue($paramConverter->apply($request, $configuration));
        $this->assertTrue($request->attributes->has('value'));
        $this->assertEquals(
            new Value(array('id' => 42, 'published' => false)),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithNoAttribute()
    {
        $request = Request::create('/');
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass(Value::class);

        $paramConverter = new ParamConverter();
        $this->assertFalse($paramConverter->apply($request, $configuration));
        $this->assertFalse($request->attributes->has('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithEmptyAndOptionalAttribute()
    {
        $request = Request::create('/');
        $request->attributes->set('id', '');
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass(Value::class);
        $configuration->setIsOptional(true);

        $paramConverter = new ParamConverter();
        $this->assertFalse($paramConverter->apply($request, $configuration));
        $this->assertFalse($request->attributes->has('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testApplyWithEmptyAndNonOptionalAttribute()
    {
        $request = Request::create('/');
        $request->attributes->set('id', '');
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass(Value::class);

        $paramConverter = new ParamConverter();
        $paramConverter->apply($request, $configuration);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::supports
     */
    public function testSupports()
    {
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass(Value::class);

        $paramConverter = new ParamConverter();
        $this->assertTrue($paramConverter->supports($configuration));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::supports
     */
    public function testSupportsInvalidClass()
    {
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass('Some\Class');

        $paramConverter = new ParamConverter();
        $this->assertFalse($paramConverter->supports($configuration));
    }
}
