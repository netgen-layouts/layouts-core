<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Type\Stubs;

use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ParameterStructFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('definition_collection')
            ->required()
            ->allowedTypes(ParameterDefinitionCollection::class);

        $resolver
            ->define('groups')
            ->required()
            ->default([])
            ->allowedTypes('string[]');

        $resolver->setAllowedTypes('data', ParameterStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'parameter_values',
            ParametersType::class,
            [
                'mapped' => false,
                'data' => $options['data'],
                'property_path' => 'parameterValues',
                'parameter_definitions' => $options['definition_collection'],
                'groups' => $options['groups'],
                'label_prefix' => 'label',
            ],
        );
    }
}
