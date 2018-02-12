<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\DateTimeMapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\DateTimeDataMapper;
use Netgen\BlockManager\Parameters\Form\Type\DateTimeType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

final class DateTimeMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\DateTimeMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new DateTimeMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\DateTimeMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(DateTimeType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\DateTimeMapper::handleForm
     */
    public function testHandleForm()
    {
        $parameterDefinition = new ParameterDefinition();

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $factory = $this->createMock(FormFactoryInterface::class);
        $formBuilder = new FormBuilder('name', null, $dispatcher, $factory);

        $this->mapper->handleForm($formBuilder, $parameterDefinition);

        $this->assertInstanceOf(DateTimeDataMapper::class, $formBuilder->getDataMapper());
    }
}
