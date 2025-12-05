<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Form;

use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\Layout\Form\CreateType;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function count;

#[CoversClass(CreateType::class)]
final class CreateTypeTest extends FormTestCase
{
    private LayoutTypeRegistry $layoutTypeRegistry;

    protected function setUp(): void
    {
        $layoutType1 = LayoutType::fromArray(
            [
                'name' => 'Test layout 1',
                'identifier' => 'test_layout_1',
                'isEnabled' => true,
            ],
        );

        $layoutType2 = LayoutType::fromArray(
            [
                'name' => 'Test layout 2',
                'identifier' => 'test_layout_2',
                'isEnabled' => false,
            ],
        );

        $this->layoutTypeRegistry = new LayoutTypeRegistry(
            [
                'test_layout_1' => $layoutType1,
                'test_layout_2' => $layoutType2,
            ],
        );

        parent::setUp();
    }

    public function testSubmitValidData(): void
    {
        $submittedData = [
            'name' => 'My layout',
            'description' => 'My layout description',
            'layoutType' => 'test_layout_1',
            'isShared' => true,
        ];

        $struct = new LayoutCreateStruct();

        $form = $this->factory->create(
            CreateType::class,
            $struct,
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame('My layout', $struct->name);
        self::assertSame('My layout description', $struct->description);
        self::assertTrue($struct->isShared);

        self::assertInstanceOf(LayoutType::class, $struct->layoutType);
        self::assertSame('test_layout_1', $struct->layoutType->identifier);

        $view = $form->createView();

        $layoutTypes = $this->layoutTypeRegistry->getLayoutTypes(true);
        $childViews = $view->children['layoutType']->children;

        self::assertCount(count($layoutTypes), $childViews);

        foreach ($layoutTypes as $identifier => $layoutType) {
            self::assertArrayHasKey($identifier, $childViews);

            self::assertArrayHasKey('layout_type', $childViews[$identifier]->vars);
            self::assertSame($layoutType, $childViews[$identifier]->vars['layout_type']);
        }
    }

    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new LayoutCreateStruct();

        $options = $optionsResolver->resolve(
            [
                'data' => $struct,
            ],
        );

        self::assertSame($struct, $options['data']);
    }

    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\Layouts\API\Values\Layout\LayoutCreateStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->define('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'data' => '',
            ],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new CreateType($this->layoutTypeRegistry);
    }
}
