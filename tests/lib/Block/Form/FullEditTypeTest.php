<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\Form\FullEditType;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FullEditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private $definition;

    /**
     * @var \Netgen\BlockManager\API\Values\Block\Block
     */
    private $block;

    public function setUp(): void
    {
        parent::setUp();

        $handler = new BlockDefinitionHandler();
        $this->definition = BlockDefinition::fromArray(
            [
                'identifier' => 'definition',
                'parameterDefinitions' => $handler->getParameterDefinitions(),
                'viewTypes' => [
                    'large' => ViewType::fromArray(
                        [
                            'name' => 'large',
                            'identifier' => 'large',
                            'itemViewTypes' => [
                                'standard' => ItemViewType::fromArray(
                                    [
                                        'name' => 'standard',
                                        'identifier' => 'standard',
                                    ]
                                ),
                            ],
                        ]
                    ),
                    'small' => ViewType::fromArray(
                        [
                            'name' => 'small',
                            'identifier' => 'small',
                            'itemViewTypes' => [
                                'standard' => ItemViewType::fromArray(
                                    [
                                        'name' => 'standard',
                                        'identifier' => 'standard',
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->block = Block::fromArray(['definition' => $this->definition, 'mainLocale' => 'en']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildView
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
            ['block' => $this->block]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame('My block', $struct->name);
        self::assertSame('large', $struct->viewType);
        self::assertSame('standard', $struct->itemViewType);

        self::assertSame(
            ['css_class' => 'Some CSS class', 'css_id' => 'Some CSS ID'],
            $struct->getParameterValues()
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
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildView
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
                    ]
                ),
            ]
        );

        self::assertTrue($form->get('name')->isDisabled());
        self::assertTrue($form->get('view_type')->isDisabled());
        self::assertTrue($form->get('item_view_type')->isDisabled());

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertTrue($form->get('parameters')->get('css_class')->isDisabled());
        self::assertTrue($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildView
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
                    ]
                ),
            ]
        );

        self::assertFalse($form->get('name')->isDisabled());
        self::assertFalse($form->get('view_type')->isDisabled());
        self::assertFalse($form->get('item_view_type')->isDisabled());

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertFalse($form->get('parameters')->get('css_class')->isDisabled());
        self::assertFalse($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
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
            ]
        );

        self::assertSame($this->block, $options['block']);
        self::assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
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
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidBlock(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "block" with value "" is expected to be of type "Netgen\\BlockManager\\API\\Values\\Block\\Block", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'block' => '',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "data" with value "" is expected to be of type "Netgen\\BlockManager\\API\\Values\\Block\\BlockUpdateStruct", but is of type "string".');

        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'block' => $this->block,
                'data' => '',
            ]
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
        return [new ParametersType(['text_line' => new TextLineMapper()])];
    }
}
