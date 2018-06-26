<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Layout\Form\CreateType;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    public function setUp(): void
    {
        $layoutType1 = new LayoutType(
            [
                'name' => '4 zones A',
                'identifier' => '4_zones_a',
                'isEnabled' => true,
            ]
        );

        $layoutType2 = new LayoutType(
            [
                'name' => '4 zones B',
                'identifier' => '4_zones_b',
                'isEnabled' => false,
            ]
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry(
            [
                '4_zones_a' => $layoutType1,
                '4_zones_b' => $layoutType2,
            ]
        );

        parent::setUp();
    }

    public function getMainType(): FormTypeInterface
    {
        return new CreateType($this->layoutTypeRegistry);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::__construct
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::buildForm
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::finishView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'name' => 'My layout',
            'layoutType' => '4_zones_a',
            'shared' => true,
        ];

        $struct = new LayoutCreateStruct();

        $form = $this->factory->create(
            CreateType::class,
            $struct
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());

        $this->assertSame('My layout', $struct->name);
        $this->assertTrue($struct->shared);

        $this->assertInstanceOf(LayoutType::class, $struct->layoutType);
        $this->assertSame('4_zones_a', $struct->layoutType->getIdentifier());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

        $this->assertSame(
            $this->layoutTypeRegistry->getLayoutTypes(true),
            $form->get('layoutType')->getConfig()->getOption('choices')
        );

        $this->assertArrayHasKey('layout_types', $view['layoutType']->vars);

        $this->assertSame(
            $this->layoutTypeRegistry->getLayoutTypes(true),
            $view['layoutType']->vars['layout_types']
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new LayoutCreateStruct();

        $options = $optionsResolver->resolve(
            [
                'data' => $struct,
            ]
        );

        $this->assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CreateType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'data' => '',
            ]
        );
    }
}
