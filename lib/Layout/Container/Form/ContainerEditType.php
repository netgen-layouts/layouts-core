<?php

namespace Netgen\BlockManager\Layout\Container\Form;

use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Validator\Constraint\Structs\ContainerUpdateStruct as ContainerUpdateStructConstraint;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContainerEditType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('container');
        $resolver->setAllowedTypes('container', Container::class);
        $resolver->setAllowedTypes('data', ContainerUpdateStruct::class);

        $resolver->setDefault('constraints', function (Options $options) {
            return array(
                new ContainerUpdateStructConstraint(
                    array(
                        'payload' => $options['container'],
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
        /** @var \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface $containerDefinition */
        $containerDefinition = $options['container']->getContainerDefinition();

        $viewTypeChoices = array();
        foreach ($containerDefinition->getConfig()->getViewTypes() as $viewType) {
            $viewTypeChoices[$viewType->getIdentifier()] = $viewType->getName();
        }

        $builder->add(
            'view_type',
            ChoiceType::class,
            array(
                'label' => 'container.view_type',
                'choices' => array_flip($viewTypeChoices),
                'choices_as_values' => true,
                'property_path' => 'viewType',
            )
        );

        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => 'container.name',
                'property_path' => 'name',
                // null and empty string have different meanings for name
                // so we set the default value to a single space (instead of
                // an empty string) because of
                // https://github.com/symfony/symfony/issues/5906
                'empty_data' => ' ',
            )
        );

        $builder->add(
            'parameters',
            ParametersType::class,
            array(
                'label' => false,
                'property_path' => 'parameterValues',
                'parameter_collection' => $containerDefinition,
                'label_prefix' => 'container.' . $containerDefinition->getIdentifier(),
            )
        );
    }
}
