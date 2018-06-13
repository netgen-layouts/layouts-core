<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Config\Form;

use Netgen\BlockManager\API\Values\Config\Config;
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

final class EditType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(['configurable', 'config_key', 'label_prefix']);

        $resolver->setAllowedTypes('config_key', ['string', 'null']);
        $resolver->setAllowedTypes('configurable', ConfigAwareValue::class);
        $resolver->setAllowedTypes('label_prefix', 'string');
        $resolver->setAllowedTypes('data', ConfigAwareStruct::class);

        $resolver->setDefault('config_key', null);
        $resolver->setDefault('constraints', function (Options $options) {
            return [
                new ConfigAwareStructConstraint(
                    [
                        'payload' => array_map(
                            function (Config $config) {
                                return $config->getDefinition();
                            },
                            $options['configurable']->getConfigs()
                        ),
                    ]
                ),
            ];
        });
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $value */
        $value = $options['configurable'];

        /** @var \Netgen\BlockManager\API\Values\Config\ConfigAwareStruct $data */
        $data = $options['data'];

        $configKeys = [$options['config_key']];
        if ($options['config_key'] === null) {
            $configKeys = array_keys($data->getConfigStructs());
        }

        foreach ($configKeys as $configKey) {
            if (!$data->hasConfigStruct($configKey)) {
                continue;
            }

            $builder->add(
                $configKey,
                ParametersType::class,
                [
                    'data' => $data->getConfigStruct($configKey),
                    'property_path' => 'configStructs[' . $configKey . ']',
                    'parameter_definitions' => $value->getConfig($configKey)->getDefinition(),
                    'label_prefix' => $options['label_prefix'] . '.' . $configKey,
                ]
            );
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['configurable'] = $options['configurable'];
    }
}
