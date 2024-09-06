<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Form;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\Layout\Form\CopyType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;

final class CopyTypeTest extends FormTestCase
{
    private Layout $layout;

    protected function setUp(): void
    {
        parent::setUp();

        $this->layout = new Layout();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Form\CopyType::buildForm
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'name' => 'New name',
            'description' => 'New description',
        ];

        $struct = new LayoutCopyStruct();

        $form = $this->factory->create(
            CopyType::class,
            $struct,
            [
                'layout' => $this->layout,
            ],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame('New name', $struct->name);
        self::assertSame('New description', $struct->description);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\Layouts\Layout\Form\CopyType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new LayoutCopyStruct();

        $options = $optionsResolver->resolve(
            [
                'layout' => $this->layout,
                'data' => $struct,
            ],
        );

        self::assertSame($this->layout, $options['layout']);
        self::assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Form\CopyType::configureOptions
     */
    public function testConfigureOptionsWithMissingLayout(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "layout" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Form\CopyType::configureOptions
     */
    public function testConfigureOptionsWithInvalidLayout(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "layout" with value "" is expected to be of type "Netgen\Layouts\API\Values\Layout\Layout", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'layout' => '',
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Layout\Form\CopyType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\Layouts\API\Values\Layout\LayoutCopyStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'layout' => $this->layout,
                'data' => '',
            ],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new CopyType();
    }
}
