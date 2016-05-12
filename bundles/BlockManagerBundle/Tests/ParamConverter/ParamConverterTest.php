<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter;

use Netgen\BlockManager\API\Values\Page\Layout;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Netgen\Bundle\BlockManagerBundle\Tests\Stubs\ParamConverter;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Symfony\Component\HttpFoundation\Request;

class ParamConverterTest extends \PHPUnit_Framework_TestCase
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
        self::assertTrue($paramConverter->apply($request, $configuration));
        self::assertTrue($request->attributes->has('value'));
        self::assertEquals(
            new Value(array('status' => Layout::STATUS_PUBLISHED)),
            $request->attributes->get('value')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithStatus()
    {
        $request = Request::create('/');
        $request->attributes->set('id', 42);
        $request->attributes->set('status', Layout::STATUS_DRAFT);
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass(Value::class);

        $paramConverter = new ParamConverter();
        self::assertTrue($paramConverter->apply($request, $configuration));
        self::assertTrue($request->attributes->has('value'));
        self::assertEquals(
            new Value(array('status' => Layout::STATUS_DRAFT)),
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
        self::assertFalse($paramConverter->apply($request, $configuration));
        self::assertFalse($request->attributes->has('value'));
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
        self::assertFalse($paramConverter->apply($request, $configuration));
        self::assertFalse($request->attributes->has('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     * @expectedException \RuntimeException
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
        self::assertTrue($paramConverter->supports($configuration));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::supports
     */
    public function testSupportsInvalidClass()
    {
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass('Some\Class');

        $paramConverter = new ParamConverter();
        self::assertFalse($paramConverter->supports($configuration));
    }
}
