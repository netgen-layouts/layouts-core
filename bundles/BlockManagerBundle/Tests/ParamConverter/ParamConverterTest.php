<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Netgen\Bundle\BlockManagerBundle\Tests\Stubs\ParamConverter;
use Netgen\BlockManager\Tests\API\Stubs\Value;
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
        $configuration->setClass('Netgen\BlockManager\Tests\API\Stubs\Value');

        $paramConverter = new ParamConverter();
        self::assertEquals(true, $paramConverter->apply($request, $configuration));
        self::assertEquals(true, $request->attributes->has('value'));
        self::assertEquals(new Value(), $request->attributes->get('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithNoAttribute()
    {
        $request = Request::create('/');
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass('Netgen\BlockManager\Tests\API\Stubs\Value');

        $paramConverter = new ParamConverter();
        self::assertEquals(false, $paramConverter->apply($request, $configuration));
        self::assertEquals(false, $request->attributes->has('value'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::apply
     */
    public function testApplyWithEmptyAndOptionalAttribute()
    {
        $request = Request::create('/');
        $request->attributes->set('id', '');
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass('Netgen\BlockManager\Tests\API\Stubs\Value');
        $configuration->setIsOptional(true);

        $paramConverter = new ParamConverter();
        self::assertEquals(false, $paramConverter->apply($request, $configuration));
        self::assertEquals(false, $request->attributes->has('value'));
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
        $configuration->setClass('Netgen\BlockManager\Tests\API\Stubs\Value');

        $paramConverter = new ParamConverter();
        $paramConverter->apply($request, $configuration);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::supports
     */
    public function testSupports()
    {
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass('Netgen\BlockManager\Tests\API\Stubs\Value');

        $paramConverter = new ParamConverter();
        self::assertEquals(true, $paramConverter->supports($configuration));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\ParamConverter::supports
     */
    public function testSupportsInvalidClass()
    {
        $configuration = new ParamConverterConfiguration(array());
        $configuration->setClass('Some\Class');

        $paramConverter = new ParamConverter();
        self::assertEquals(false, $paramConverter->supports($configuration));
    }
}
