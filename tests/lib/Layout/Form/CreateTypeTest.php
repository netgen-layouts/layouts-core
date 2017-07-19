<?php

namespace Netgen\BlockManager\Tests\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Layout\Form\CreateType;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    public function setUp()
    {
        $this->layoutTypeRegistry = new LayoutTypeRegistry();

        $this->layoutTypeRegistry->addLayoutType(
            '4_zones_a',
            new LayoutType(
                array(
                    'name' => '4 zones A',
                    'identifier' => '4_zones_a',
                    'isEnabled' => true,
                )
            )
        );

        $this->layoutTypeRegistry->addLayoutType(
            '4_zones_b',
            new LayoutType(
                array(
                    'name' => '4 zones B',
                    'identifier' => '4_zones_b',
                    'isEnabled' => false,
                )
            )
        );

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new CreateType($this->layoutTypeRegistry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::__construct
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::buildForm
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'name' => 'My layout',
            'layoutType' => '4_zones_a',
            'shared' => true,
        );

        $updatedStruct = new LayoutCreateStruct();
        $updatedStruct->name = 'My layout';
        $updatedStruct->layoutType = new LayoutType(
            array(
                'name' => '4 zones A',
                'identifier' => '4_zones_a',
                'isEnabled' => true,
            )
        );
        $updatedStruct->shared = true;

        $form = $this->factory->create(
            CreateType::class,
            new LayoutCreateStruct()
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

        $this->assertEquals(
            array('4 zones A' => '4_zones_a'),
            $form->get('layoutType')->getConfig()->getOption('choices')
        );

        $this->assertArrayHasKey('layout_types', $view->vars);
        $this->assertEquals($view->vars['layout_types'], $this->layoutTypeRegistry->getLayoutTypes(true));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'data' => new LayoutCreateStruct(),
            )
        );

        $this->assertEquals(new LayoutCreateStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'data' => '',
            )
        );
    }
}
