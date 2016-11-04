<?php

namespace Netgen\BlockManager\Parameters\Form\Type;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Parameters\Form\MapperInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class ParametersType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\MapperInterface[]
     */
    protected $mappers = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Form\MapperInterface[] $mappers
     */
    public function __construct(array $mappers = array())
    {
        foreach ($mappers as $mapper) {
            if (!$mapper instanceof MapperInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Parameter form mapper "%s" needs to implement MapperInterface.',
                        get_class($mapper)
                    )
                );
            }
        }

        $this->mappers = $mappers;
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
                'property_path_prefix',
            )
        );

        $resolver->setAllowedTypes('parameters', 'array');
        $resolver->setAllowedTypes('label_prefix', 'string');
        $resolver->setAllowedTypes('property_path_prefix', 'string');

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
            $parameterType = $parameter->getType();

            if (!isset($this->mappers[$parameterType])) {
                throw new RuntimeException(
                    sprintf(
                        'No parameter form mapper found for "%s" parameter type.',
                        $parameterType
                    )
                );
            }

            $mapper = $this->mappers[$parameterType];

            $defaultOptions = array(
                'required' => $parameter->isRequired(),
                'label' => $options['label_prefix'] . '.' . $parameterName,
                'property_path' => $options['property_path_prefix'] . '[' . $parameterName . ']',
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

            $builder->add($parameterForm);
        }
    }
}
