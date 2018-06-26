<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Form;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithTranslatableCompoundParameter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithUntranslatableCompoundParameter;
use Netgen\BlockManager\Tests\Form\Stubs\TranslatableTypeStub;
use Netgen\BlockManager\Tests\Parameters\Stubs\FormMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;

final class TranslatableTypeTest extends FormTestCase
{
    public function getMainType(): FormTypeInterface
    {
        return new TranslatableTypeStub();
    }

    public function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }

    public function getTypes(): array
    {
        $formMappers = [
            'text_line' => new FormMapper(),
            'compound_boolean' => new FormMapper(true),
        ];

        return [new ParametersType($formMappers)];
    }

    /**
     * @covers \Netgen\BlockManager\Form\TranslatableType::disableUntranslatableForms
     */
    public function testDisableUntranslatableFormsWithTranslatableCompoundParameter(): void
    {
        $handler = new BlockDefinitionHandlerWithTranslatableCompoundParameter();
        $block = new Block(
            [
                'definition' => new BlockDefinition(
                    [
                        'parameterDefinitions' => $handler->getParameterDefinitions(),
                    ]
                ),
            ]
        );

        $form = $this->factory->create(
            TranslatableTypeStub::class,
            new BlockUpdateStruct(),
            ['block' => $block]
        );

        $this->assertTrue($form->get('name')->isDisabled());

        $paramsForm = $form->get('parameters');
        $this->assertFalse($paramsForm->isDisabled());

        $this->assertFalse($paramsForm->get('compound')->isDisabled());
        $this->assertFalse($paramsForm->get('compound')->get('inner')->isDisabled());

        $this->assertFalse($paramsForm->get('css_class')->isDisabled());
        $this->assertTrue($paramsForm->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Form\TranslatableType::disableUntranslatableForms
     */
    public function testDisableUntranslatableFormsWithUntranslatableCompoundParameter(): void
    {
        $handler = new BlockDefinitionHandlerWithUntranslatableCompoundParameter();
        $block = new Block(
            [
                'definition' => new BlockDefinition(
                    [
                        'parameterDefinitions' => $handler->getParameterDefinitions(),
                    ]
                ),
            ]
        );

        $form = $this->factory->create(
            TranslatableTypeStub::class,
            new BlockUpdateStruct(),
            ['block' => $block]
        );

        $this->assertTrue($form->get('name')->isDisabled());

        $paramsForm = $form->get('parameters');
        $this->assertFalse($paramsForm->isDisabled());

        $this->assertTrue($paramsForm->get('compound')->isDisabled());
        $this->assertTrue($paramsForm->get('compound')->get('inner')->isDisabled());

        $this->assertFalse($paramsForm->get('css_class')->isDisabled());
        $this->assertTrue($paramsForm->get('css_id')->isDisabled());
        $this->assertTrue($paramsForm->get('other')->isDisabled());
    }
}
