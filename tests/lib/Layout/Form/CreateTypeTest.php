<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Layout\Form\CreateType;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Locale\LocaleProviderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $localeProviderMock;

    public function setUp(): void
    {
        $this->layoutTypeRegistry = new LayoutTypeRegistry();

        $this->layoutTypeRegistry->addLayoutType(
            '4_zones_a',
            new LayoutType(
                [
                    'name' => '4 zones A',
                    'identifier' => '4_zones_a',
                    'isEnabled' => true,
                ]
            )
        );

        $this->layoutTypeRegistry->addLayoutType(
            '4_zones_b',
            new LayoutType(
                [
                    'name' => '4 zones B',
                    'identifier' => '4_zones_b',
                    'isEnabled' => false,
                ]
            )
        );

        $this->localeProviderMock = $this->createMock(LocaleProviderInterface::class);
        $this->localeProviderMock
            ->expects($this->any())
            ->method('getAvailableLocales')
            ->will($this->returnValue(['en' => 'English']));

        parent::setUp();
    }

    public function getMainType(): FormTypeInterface
    {
        return new CreateType($this->layoutTypeRegistry, $this->localeProviderMock);
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
            'mainLocale' => 'en',
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
        $this->assertSame('en', $struct->mainLocale);

        $this->assertInstanceOf(LayoutType::class, $struct->layoutType);
        $this->assertEquals('4_zones_a', $struct->layoutType->getIdentifier());

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
