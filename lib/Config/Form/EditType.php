<?php

declare(strict_types=1);

namespace Netgen\Layouts\Config\Form;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigAwareStruct;
use Netgen\Layouts\API\Values\Config\ConfigAwareValue;
use Netgen\Layouts\Config\ConfigDefinitionInterface;
use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Netgen\Layouts\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;
use function array_map;

final class EditType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setAllowedTypes('data', ConfigAwareStruct::class);

        $resolver->setDefault(
            'constraints',
            static fn (Options $options): array => [
                new ConfigAwareStructConstraint(
                    payload: array_map(
                        static fn (Config $config): ConfigDefinitionInterface => $config->definition,
                        $options['configurable']->configs->toArray(),
                    ),
                ),
            ],
        );

        $resolver
            ->define('configurable')
            ->required()
            ->allowedTypes(ConfigAwareValue::class);

        $resolver
            ->define('config_key')
            ->required()
            ->default(null)
            ->allowedTypes('string', 'null');

        $resolver
            ->define('label_prefix')
            ->required()
            ->allowedTypes('string');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Netgen\Layouts\API\Values\Config\ConfigAwareValue $value */
        $value = $options['configurable'];

        /** @var \Netgen\Layouts\API\Values\Config\ConfigAwareStruct $data */
        $data = $options['data'];

        $configKeys = [$options['config_key']];
        if ($options['config_key'] === null) {
            $configKeys = array_keys($data->configStructs);
        }

        foreach ($configKeys as $configKey) {
            if (!$data->hasConfigStruct($configKey)) {
                continue;
            }

            $builder->add(
                $configKey,
                ParametersType::class,
                [
                    'mapped' => false,
                    'data' => $data->getConfigStruct($configKey),
                    'property_path' => 'configStructs[' . $configKey . ']',
                    'parameter_definitions' => $value->getConfig($configKey)->definition,
                    'label_prefix' => $options['label_prefix'] . '.' . $configKey,
                ],
            );
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['configurable'] = $options['configurable'];
    }
}
