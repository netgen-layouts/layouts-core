<?php

namespace Netgen\BlockManager\Tests\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Layout\Form\CreateType;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $localeProviderMock;

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

        $this->localeProviderMock = $this->createMock(LocaleProviderInterface::class);
        $this->localeProviderMock
            ->expects($this->any())
            ->method('getAvailableLocales')
            ->will($this->returnValue(array('en' => 'English')));

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new CreateType($this->layoutTypeRegistry, $this->localeProviderMock);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::__construct
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::buildForm
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::finishView
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'name' => 'My layout',
            'layoutType' => '4_zones_a',
            'shared' => true,
            'mainLocale' => 'en',
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
        $updatedStruct->mainLocale = 'en';

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
            $this->layoutTypeRegistry->getLayoutTypes(true),
            $form->get('layoutType')->getConfig()->getOption('choices')
        );

        $this->assertArrayHasKey('layout_types', $view['layoutType']->vars);

        $this->assertEquals(
            $this->layoutTypeRegistry->getLayoutTypes(true),
            $view['layoutType']->vars['layout_types']
        );
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
