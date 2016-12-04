<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\Form\Type\LinkType;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType\LinkType as LinkParameterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

class LinkMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new LinkMapper(array('default'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(LinkType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::mapOptions
     */
    public function testMapOptions()
    {
        $parameter = new Parameter(
            array(
                'type' => new LinkParameterType(),
                'options' => array(
                    'value_types' => array('value'),
                ),
            )
        );

        $this->assertEquals(
            array(
                'label' => false,
                'value_types' => array('value'),
            ),
            $this->mapper->mapOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::mapOptions
     */
    public function testMapOptionsWithEmptyValueTypes()
    {
        $parameter = new Parameter(
            array(
                'type' => new LinkParameterType(),
                'options' => array(
                    'value_types' => array(),
                ),
            )
        );

        $this->assertEquals(
            array(
                'label' => false,
                'value_types' => array('default'),
            ),
            $this->mapper->mapOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\LinkMapper::handleForm
     */
    public function testHandleForm()
    {
        $parameter = new Parameter(
            array(
                'type' => new LinkParameterType(),
            )
        );

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $factory = $this->createMock(FormFactoryInterface::class);
        $formBuilder = new FormBuilder('name', null, $dispatcher, $factory);

        $this->mapper->handleForm($formBuilder, $parameter);

        $this->assertInstanceOf(LinkDataMapper::class, $formBuilder->getDataMapper());
    }
}
