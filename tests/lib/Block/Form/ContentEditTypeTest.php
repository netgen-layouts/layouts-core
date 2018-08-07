<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\Form\ContentEditType;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContentEditTypeTest extends FormTestCase
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

        $handler = new BlockDefinitionHandler(['content']);

        $this->definition = BlockDefinition::fromArray(
            [
                'identifier' => 'block_definition',
                'handler' => $handler,
                'forms' => [
                    'content' => Form::fromArray(
                        [
                            'identifier' => 'content',
                            'type' => ContentEditType::class,
                        ]
                    ),
                ],
                'viewTypes' => [
                    'large' => ViewType::fromArray(
                        [
                            'identifier' => 'large',
                            'name' => 'Large',
                            'itemViewTypes' => [
                                'standard' => ItemViewType::fromArray(
                                    [
                                        'identifier' => 'standard',
                                        'name' => 'Standard',
                                    ]
                                ),
                            ],
                        ]
                    ),
                    'small' => ViewType::fromArray(
                        [
                            'identifier' => 'large',
                            'name' => 'Large',
                            'itemViewTypes' => [
                                'standard' => ItemViewType::fromArray(
                                    [
                                        'identifier' => 'standard',
                                        'name' => 'Standard',
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            ]
        );

        $this->block = Block::fromArray(['definition' => $this->definition, 'mainLocale' => 'en']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'parameters' => [
                'css_class' => 'Some CSS class',
            ],
            'name' => 'My block',
        ];

        $struct = new BlockUpdateStruct();
        $struct->locale = 'en';

        $form = $this->factory->create(
            ContentEditType::class,
            $struct,
            ['block' => $this->block]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertSame('My block', $struct->name);
        self::assertSame(['css_class' => 'Some CSS class'], $struct->getParameterValues());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }

        foreach (array_keys($submittedData['parameters']) as $key) {
            self::assertArrayHasKey($key, $children['parameters']);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::buildView
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     */
    public function testDisableUntranslatableFormsOnNonMainLocale(): void
    {
        $struct = new BlockUpdateStruct();
        $struct->locale = 'hr';

        $form = $this->factory->create(
            ContentEditType::class,
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

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertTrue($form->get('parameters')->get('css_class')->isDisabled());
        self::assertTrue($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::buildView
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     */
    public function testDisableUntranslatableFormsOnMainLocale(): void
    {
        $struct = new BlockUpdateStruct();
        $struct->locale = 'en';

        $form = $this->factory->create(
            ContentEditType::class,
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

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertFalse($form->get('parameters')->get('css_class')->isDisabled());
        self::assertFalse($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::configureOptions
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
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "block" is missing.
     */
    public function testConfigureOptionsWithMissingBlock(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "block" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Block\Block", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidBlock(): void
    {
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
     * @covers \Netgen\BlockManager\Block\Form\ContentEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Block\BlockUpdateStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
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
        return new ContentEditType();
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
