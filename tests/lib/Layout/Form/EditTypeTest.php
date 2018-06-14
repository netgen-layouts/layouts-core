<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Form\EditType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    public function setUp(): void
    {
        parent::setUp();

        $this->layout = new Layout();
    }

    public function getMainType(): FormTypeInterface
    {
        return new EditType();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\EditType::buildForm
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'name' => 'New name',
            'description' => 'New description',
        ];

        $updatedStruct = new LayoutUpdateStruct();
        $updatedStruct->name = 'New name';
        $updatedStruct->description = 'New description';

        $form = $this->factory->create(
            EditType::class,
            new LayoutUpdateStruct(),
            [
                'layout' => $this->layout,
            ]
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\EditType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'layout' => $this->layout,
                'data' => new LayoutUpdateStruct(),
            ]
        );

        $this->assertEquals($this->layout, $options['layout']);
        $this->assertEquals(new LayoutUpdateStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "layout" is missing.
     */
    public function testConfigureOptionsWithMissingLayout(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "layout" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Layout\Layout", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidLayout(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'layout' => '',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\EditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'layout' => $this->layout,
                'data' => '',
            ]
        );
    }
}
