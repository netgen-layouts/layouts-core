<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Form;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Form;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Block\Form\ContentEditType;
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

final class ContentEditTypeTest extends FormTestCase
{
    private BlockDefinition $definition;

    private Block $block;

    protected function setUp(): void
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
                        ],
                    ),
                ],
                'configProvider' => ConfigProvider::fromFullConfig(
                    [
                        'large' => ViewType::fromArray(
                            [
                                'identifier' => 'large',
                                'name' => 'Large',
                                'itemViewTypes' => [
                                    'standard' => ItemViewType::fromArray(
                                        [
                                            'identifier' => 'standard',
                                            'name' => 'Standard',
                                        ],
                                    ),
                                ],
                            ],
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
                                        ],
                                    ),
                                ],
                            ],
                        ),
                    ],
                ),
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            ],
        );

        $this->block = Block::fromArray(['definition' => $this->definition, 'mainLocale' => 'en']);
    }

    /**
     * @covers \Netgen\Layouts\Block\Form\ContentEditType::buildForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addParametersForm
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
            ['block' => $this->block],
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
     * @covers \Netgen\Layouts\Block\Form\ContentEditType::buildForm
     * @covers \Netgen\Layouts\Block\Form\ContentEditType::buildView
     * @covers \Netgen\Layouts\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addParametersForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\Layouts\Block\Form\EditType::processViewTypeConfig
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
                    ],
                ),
            ],
        );

        self::assertTrue($form->get('name')->isDisabled());

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertTrue($form->get('parameters')->get('css_class')->isDisabled());
        self::assertTrue($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\Layouts\Block\Form\ContentEditType::buildForm
     * @covers \Netgen\Layouts\Block\Form\ContentEditType::buildView
     * @covers \Netgen\Layouts\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addParametersForm
     * @covers \Netgen\Layouts\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\Layouts\Block\Form\EditType::processViewTypeConfig
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
                    ],
                ),
            ],
        );

        self::assertFalse($form->get('name')->isDisabled());

        self::assertFalse($form->get('parameters')->isDisabled());
        self::assertFalse($form->get('parameters')->get('css_class')->isDisabled());
        self::assertFalse($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\Layouts\Block\Form\ContentEditType::configureOptions
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
     * @covers \Netgen\Layouts\Block\Form\ContentEditType::configureOptions
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
     * @covers \Netgen\Layouts\Block\Form\ContentEditType::configureOptions
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
     * @covers \Netgen\Layouts\Block\Form\ContentEditType::configureOptions
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
        return new ContentEditType();
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
