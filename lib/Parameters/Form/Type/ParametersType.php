<?php

namespace Netgen\BlockManager\Parameters\Form\Type;

use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class ParametersType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\FormMapperRegistryInterface
     */
    protected $formMapperRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Registry\FormMapperRegistryInterface $formMapperRegistry
     */
    public function __construct(FormMapperRegistryInterface $formMapperRegistry)
    {
        $this->formMapperRegistry = $formMapperRegistry;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(
            array(
                'parameters',
                'label_prefix',
            )
        );

        $resolver->setAllowedTypes('parameters', 'array');
        $resolver->setAllowedTypes('label_prefix', 'string');

        $resolver->setDefault('inherit_data', true);
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters */
        $parameters = $options['parameters'];

        foreach ($parameters as $parameterName => $parameter) {
            $mapper = $this->formMapperRegistry->getFormMapper($parameter->getType());

            $defaultOptions = array(
                'label' => $options['label_prefix'] . '.' . $parameterName,
                'property_path' => $options['property_path'] . '[' . $parameterName . ']',
            );

            $parameterForm = $builder->create(
                $parameterName,
                $mapper->getFormType(),
                $mapper->mapOptions(
                    $parameter,
                    $parameterName,
                    $options
                ) + $defaultOptions
            );

            $mapper->handleForm($parameter, $parameterForm);

            if ($parameter instanceof CompoundParameterInterface) {
                $this->buildForm(
                    $parameterForm,
                    array(
                        'parameters' => $parameter->getParameters(),
                    ) + $options
                );
            }

            $builder->add($parameterForm);
        }
    }
}
