<?php

namespace Netgen\BlockManager\Layout\Container\Form;

use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Validator\Constraint\Structs\PlaceholderUpdateStruct as PlaceholderUpdateStructConstraint;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceholderEditType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('placeholder');
        $resolver->setAllowedTypes('placeholder', Placeholder::class);
        $resolver->setAllowedTypes('data', PlaceholderUpdateStruct::class);

        $resolver->setDefault('constraints', function (Options $options) {
            return array(
                new PlaceholderUpdateStructConstraint(
                    array(
                        'payload' => $options['placeholder'],
                    )
                ),
            );
        });
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface $placeholderDefinition */
        $placeholderDefinition = $options['placeholder']->getPlaceholderDefinition();

        $builder->add(
            'parameters',
            ParametersType::class,
            array(
                'label' => false,
                'property_path' => 'parameterValues',
                'parameter_collection' => $placeholderDefinition,
                'label_prefix' => 'placeholder.' . $placeholderDefinition->getIdentifier(),
            )
        );
    }
}
