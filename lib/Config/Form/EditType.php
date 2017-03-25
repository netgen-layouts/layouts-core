<?php

namespace Netgen\BlockManager\Config\Form;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('configurable', 'configIdentifiers'));

        $resolver->setAllowedTypes('configIdentifiers', 'array');
        $resolver->setAllowedTypes('configurable', ConfigAwareValue::class);
        $resolver->setAllowedTypes('data', ConfigAwareStruct::class);

        $resolver->setDefault('configIdentifiers', array());
        $resolver->setDefault('constraints', function (Options $options) {
            return array(
                new ConfigAwareStructConstraint(
                    array(
                        'payload' => $options['configurable'],
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
        /** @var \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $configAwareValue */
        $configAwareValue = $options['configurable'];
        $configs = $configAwareValue->getConfigs();

        $configType = $configAwareValue->getConfigCollection()->getConfigType();

        /** @var \Netgen\BlockManager\API\Values\Config\ConfigStruct[] $configStructs */
        $configStructs = $options['data']->getConfigStructs();

        $configIdentifiers = $options['configIdentifiers'];
        if (empty($configIdentifiers)) {
            $configIdentifiers = array_keys($configStructs);
        }

        foreach ($configIdentifiers as $identifier) {
            $builder->add(
                $identifier,
                ParametersType::class,
                array(
                    'data' => $configStructs[$identifier],
                    'label' => 'config.' . $configType . '.' . $identifier,
                    'property_path' => 'configStructs[' . $identifier . ']',
                    'parameter_collection' => $configs[$identifier]->getDefinition(),
                    'label_prefix' => 'config.' . $configType . '.' . $identifier,
                )
            );
        }
    }
}
