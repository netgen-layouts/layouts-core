<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Form\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Form\TranslatableTypeTrait;
use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TranslatableTypeStub extends AbstractType
{
    use TranslatableTypeTrait;

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('block');
        $resolver->setAllowedTypes('block', Block::class);
        $resolver->setAllowedTypes('data', BlockUpdateStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'property_path' => 'name',
            ],
        );

        $builder->add(
            'parameters',
            ParametersType::class,
            [
                'inherit_data' => true,
                'label_prefix' => 'label_prefix',
                'property_path' => 'parameterValues',
                'parameter_definitions' => $options['block']->getDefinition(),
            ],
        );

        $this->disableUntranslatableForms($builder);
    }
}
