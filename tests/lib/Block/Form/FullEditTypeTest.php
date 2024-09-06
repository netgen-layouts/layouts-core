<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Form;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Block\Form\FullEditType;
use Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\Layouts\Parameters\Form\Mapper\TextLineMapper;
use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;

final class FullEditTypeTest extends FormTestCase
{
    private BlockDefinition $definition;

    private Block $block;

    protected function setUp(): void
    {
        parent::setUp();

        $handler = new BlockDefinitionHandler();
        $this->definition = BlockDefinition::fromArray(
            [
                'identifier' => 'definition',
                'parameterDefinitions' => $handler->getParameterDefinitions(),
                'configProvider' => ConfigProvider::fromFullConfig(
                    [
                        'large' => ViewType::fromArray(
                            [
                                'name' => 'large',
                                'identifier' => 'large',
                                'validParameters' => null,
                                'itemViewTypes' => [
                                    'standard' => ItemViewType::fromArray(
                                        [
                                            'name' => 'standard',
                                            'identifier' => 'standard',
                                        ],
                                    ),
                                ],
                            ],
                        ),
                        'small' => ViewType::fromArray(
                            [
                                'name' => 'small',
                                'identifier' => 'small',
                                'validParameters' => null,
                                'itemViewTypes' => [
                                    'standard' => ItemViewType::fromArray(
                                        [
                                            'name' => 'standard',
                                            'identifier' => 'standard',
                                        ],
                                    ),
                                ],
                            ],
                        ),
                    ],
                ),
            ],
        );

        $this->block = Block::fromArray(['definition' => $this->definition, 'mainLocale' => 'en']);
    }

    /**
     * @covers \Netgen\Layouts\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addParametersForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\Layouts\Block\Form\EditType::processViewTypeConfig
     * @covers \Netgen\Layouts\Block\Form\FullEditType::buildForm
     * @covers \Netgen\Layouts\Block\Form\FullEditType::buildView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'parameters' => [
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ],
            'view_type' => 'large',
            'item_view_type' => 'standard',
            'name' => 'My block',
        ];

        $struct = new BlockUpdateStruct();
        $struct->locale = 'en';

        $form = $this->factory->create(
            FullEditType::class,
            $struct,
            ['block' => $this->block],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame('My block', $struct->name);
        self::assertSame('large', $struct->viewType);
        self::assertSame('standard', $struct->itemViewType);

        self::assertSame(
            ['css_class' => 'Some CSS class', 'css_id' => 'Some CSS ID'],
            $struct->getParameterValues(),
        );

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }

        foreach (array_keys($submittedData['parameters']) as $key) {
            self::assertArrayHasKey($key, $children['parameters']);
        }

        self::assertArrayHasKey('block', $view->vars);
        self::assertSame($this->block, $view->vars['block']);

        self::assertArrayHasKey('parameter_view_types', $view->vars);
    }

    /**
     * @covers \Netgen\Layouts\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addParametersForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\Layouts\Block\Form\EditType::processViewTypeConfig
     * @covers \Netgen\Layouts\Block\Form\FullEditType::buildForm
     * @covers \Netgen\Layouts\Block\Form\FullEditType::buildView
     */
    public function testDisableUntranslatableFormsOnNonMainLocale(): void
    {
        $struct = new BlockUpdateStruct();
        $struct->locale = 'hr';

        $form = $this->factory->create(
            FullEditType::class,
            $struct,
            [
                'block' => Block::fromArray(
                    [
                        'definition' => $this->definition,
                        'mainLocale' => 'en',
                    ],
                ),
            ],
        );

        self::assertTrue($form->get('name')->isDisabled());
        self::assertTrue($form->get('view_type')->isDisabled());
        self::assertTrue($form->get('item_view_type')->isDisabled());

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertTrue($form->get('parameters')->get('css_class')->isDisabled());
        self::assertTrue($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\Layouts\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addParametersForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\Layouts\Block\Form\EditType::processViewTypeConfig
     * @covers \Netgen\Layouts\Block\Form\FullEditType::buildForm
     * @covers \Netgen\Layouts\Block\Form\FullEditType::buildView
     */
    public function testDisableUntranslatableFormsOnMainLocale(): void
    {
        $struct = new BlockUpdateStruct();
        $struct->locale = 'en';

        $form = $this->factory->create(
            FullEditType::class,
            $struct,
            [
                'block' => Block::fromArray(
                    [
                        'definition' => $this->definition,
                        'mainLocale' => 'en',
                    ],
                ),
            ],
        );

        self::assertFalse($form->get('name')->isDisabled());
        self::assertFalse($form->get('view_type')->isDisabled());
        self::assertFalse($form->get('item_view_type')->isDisabled());

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertFalse($form->get('parameters')->get('css_class')->isDisabled());
        self::assertFalse($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\Layouts\Block\Form\FullEditType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new BlockUpdateStruct();

        $options = $optionsResolver->resolve(
            [
                'block' => $this->block,
                'data' => $struct,
            ],
        );

        self::assertSame($this->block, $options['block']);
        self::assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\Layouts\Block\Form\FullEditType::configureOptions
     */
    public function testConfigureOptionsWithMissingBlock(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "block" is missing.');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\Layouts\Block\Form\FullEditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidBlock(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "block" with value "" is expected to be of type "Netgen\Layouts\API\Values\Block\Block", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'block' => '',
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\Form\FullEditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\Layouts\API\Values\Block\BlockUpdateStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'block' => $this->block,
                'data' => '',
            ],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new FullEditType();
    }

    protected function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }

    protected function getTypes(): array
    {
        return [new ParametersType(new Container(['text_line' => new TextLineMapper()]))];
    }
}
