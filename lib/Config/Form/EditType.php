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
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('configurable', 'config_key', 'label_prefix'));

        $resolver->setAllowedTypes('config_key', array('string', 'null'));
        $resolver->setAllowedTypes('configurable', ConfigAwareValue::class);
        $resolver->setAllowedTypes('label_prefix', 'string');
        $resolver->setAllowedTypes('data', ConfigAwareStruct::class);

        $resolver->setDefault('config_key', null);
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
        /** @var \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $value */
        $value = $options['configurable'];

        /** @var \Netgen\BlockManager\API\Values\Config\ConfigAwareStruct $data */
        $data = $options['data'];

        $configKeys = array($options['config_key']);
        if ($options['config_key'] === null) {
            $configKeys = array_keys($data->getConfigStructs());
        }

        foreach ($configKeys as $configKey) {
            if (!$data->hasConfigStruct($configKey) || !$value->isConfigEnabled($configKey)) {
                continue;
            }

            $builder->add(
                $configKey,
                ParametersType::class,
                array(
                    'data' => $data->getConfigStruct($configKey),
                    'property_path' => 'configStructs[' . $configKey . ']',
                    'parameter_collection' => $value->getConfig($configKey)->getDefinition(),
                    'label_prefix' => $options['label_prefix'] . '.' . $configKey,
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
        $view->vars['configurable'] = $options['configurable'];
    }
}
