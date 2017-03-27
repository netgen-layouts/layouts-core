<?php

namespace Netgen\BlockManager\Config\Form;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditType extends AbstractType
{
    /**
     * @var string[]
     */
    protected $enabledConfigs = array();

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
            $configDefinition = $configs[$identifier]->getDefinition();
            if (!$configDefinition->isEnabled($configAwareValue)) {
                continue;
            }

            $this->enabledConfigs[$identifier] = $configs[$identifier];

            $builder->add(
                $identifier,
                ParametersType::class,
                array(
                    'data' => $configStructs[$identifier],
                    'label' => 'config.' . $configType . '.' . $identifier,
                    'property_path' => 'configStructs[' . $identifier . ']',
                    'parameter_collection' => $configDefinition,
                    'label_prefix' => 'config.' . $configType . '.' . $identifier,
                )
            );
        }
    }

    /**
     * Builds the form view.
     *
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['enabled_configs'] = $this->enabledConfigs;
        $view->vars['configurable'] = $options['configurable'];
    }
}
