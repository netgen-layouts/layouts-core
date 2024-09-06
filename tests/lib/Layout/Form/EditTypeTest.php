<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Form;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\Layout\Form\EditType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;

final class EditTypeTest extends FormTestCase
{
    private Layout $layout;

    protected function setUp(): void
    {
        parent::setUp();

        $this->layout = Layout::fromArray(['id' => Uuid::uuid4()]);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Form\EditType::buildForm
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'name' => 'New name',
            'description' => 'New description',
        ];

        $struct = new LayoutUpdateStruct();

        $form = $this->factory->create(
            EditType::class,
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
     * @covers \Netgen\Layouts\Layout\Form\EditType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new LayoutUpdateStruct();

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
     * @covers \Netgen\Layouts\Layout\Form\EditType::configureOptions
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
     * @covers \Netgen\Layouts\Layout\Form\EditType::configureOptions
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
     * @covers \Netgen\Layouts\Layout\Form\EditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\Layouts\API\Values\Layout\LayoutUpdateStruct", but is of type "string".');

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
        return new EditType();
    }
}
