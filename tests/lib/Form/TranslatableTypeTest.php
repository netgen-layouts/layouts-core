<?php

namespace Netgen\BlockManager\Tests\Form;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistry;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithTranslatableCompoundParameter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithUntranslatableCompoundParameter;
use Netgen\BlockManager\Tests\Form\Stubs\TranslatableTypeStub;
use Netgen\BlockManager\Tests\Parameters\Stubs\FormMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;

final class TranslatableTypeTest extends FormTestCase
{
    public function getMainType()
    {
        return new TranslatableTypeStub();
    }

    public function getTypeExtensions()
    {
        return [new ParametersTypeExtension()];
    }

    public function getTypes()
    {
        $formMapperRegistry = new FormMapperRegistry();
        $formMapperRegistry->addFormMapper('text_line', new FormMapper());
        $formMapperRegistry->addFormMapper('compound_boolean', new FormMapper(true));

        return [new ParametersType($formMapperRegistry)];
    }

    /**
     * @covers \Netgen\BlockManager\Form\TranslatableType::disableFormsOnNonMainLocale
     */
    public function testDisableUntranslatableFormsWithTranslatableCompoundParameter()
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
     * @covers \Netgen\BlockManager\Form\TranslatableType::disableFormsOnNonMainLocale
     */
    public function testDisableUntranslatableFormsWithUntranslatableCompoundParameter()
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
