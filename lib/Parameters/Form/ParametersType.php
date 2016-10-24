<?php

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class ParametersType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface
     */
    protected $formMapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface $formMapper
     */
    public function __construct(FormMapperInterface $formMapper)
    {
        $this->formMapper = $formMapper;
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
        foreach ($options['parameters'] as $parameterName => $parameter) {
            $this->formMapper->mapParameter(
                $builder,
                $parameter,
                $parameterName,
                array(
                    'label_prefix' => $options['label_prefix'],
                    'property_path_prefix' => $options['property_path_prefix'],
                )
            );
        }
    }
}
