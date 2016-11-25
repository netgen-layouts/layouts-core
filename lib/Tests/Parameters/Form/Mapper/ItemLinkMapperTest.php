<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterMapper;

use Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType\ItemLinkType as ItemLinkParameterType;
use Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserDynamicType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use PHPUnit\Framework\TestCase;

class ItemLinkMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new ItemLinkMapper(array('default'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserDynamicType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::mapOptions
     */
    public function testMapOptions()
    {
        $parameter = new Parameter(
            'name',
            new ItemLinkParameterType(),
            array(
                'value_types' => array('value'),
            )
        );

        $this->assertEquals(
            array(
                'item_types' => array('value'),
            ),
            $this->mapper->mapOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::mapOptions
     */
    public function testMapOptionsWithEmptyValueTypes()
    {
        $parameter = new Parameter(
            'name',
            new ItemLinkParameterType(),
            array(
                'value_types' => array(),
            )
        );

        $this->assertEquals(
            array(
                'item_types' => array('default'),
            ),
            $this->mapper->mapOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\ItemLinkMapper::handleForm
     */
    public function testHandleForm()
    {
        $parameter = new Parameter('name', new ItemLinkParameterType());

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $factory = $this->createMock(FormFactoryInterface::class);
        $formBuilder = new FormBuilder('name', null, $dispatcher, $factory);

        $this->mapper->handleForm($formBuilder, $parameter);

        $this->assertInstanceOf(ItemLinkDataMapper::class, $formBuilder->getDataMapper());
    }
}
