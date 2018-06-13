<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Form;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Form\SetMainLocaleType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SetMainLocaleTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    public function setUp()
    {
        $this->layout = new Layout(['availableLocales' => ['en_GB', 'hr_HR', 'fr_FR'], 'mainLocale' => 'en_GB']);

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new SetMainLocaleType();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\SetMainLocaleType::buildForm
     * @covers \Netgen\BlockManager\Layout\Form\SetMainLocaleType::buildView
     */
    public function testSubmitValidData()
    {
        $submittedData = [
            'mainLocale' => 'hr_HR',
        ];

        $form = $this->factory->create(
            SetMainLocaleType::class,
            null,
            [
                'layout' => $this->layout,
            ]
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($submittedData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\SetMainLocaleType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $layout = new Layout();
        $options = ['layout' => $layout];
        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertEquals($layout, $resolvedOptions['layout']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\SetMainLocaleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "layout" with value 42 is expected to be of type "Netgen\BlockManager\API\Values\Layout\Layout", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidLayout()
    {
        $optionsResolver = new OptionsResolver();
        $this->formType->configureOptions($optionsResolver);
        $optionsResolver->resolve(['layout' => 42]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\SetMainLocaleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "layout" is missing.
     */
    public function testConfigureOptionsWithMissingLayout()
    {
        $optionsResolver = new OptionsResolver();
        $this->formType->configureOptions($optionsResolver);
        $optionsResolver->resolve();
    }
}
