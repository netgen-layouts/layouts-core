<?php

namespace Netgen\BlockManager\Block\Form;

use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

abstract class EditType extends AbstractType
{
    const TRANSLATION_DOMAIN = 'ngbm_block_forms';

    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface
     */
    protected $parameterFormMapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface $parameterFormMapper
     */
    public function __construct(FormMapperInterface $parameterFormMapper)
    {
        $this->parameterFormMapper = $parameterFormMapper;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('blockDefinition');
        $resolver->setAllowedTypes('blockDefinition', BlockDefinitionInterface::class);
        $resolver->setAllowedTypes('data', BlockUpdateStruct::class);
        $resolver->setDefault('translation_domain', self::TRANSLATION_DOMAIN);
    }

    protected function addViewTypeForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $options['blockDefinition'];

        $choices = array();
        foreach ($blockDefinition->getConfig()->getViewTypes() as $viewType) {
            $choices[$viewType->getName()] = $viewType->getIdentifier();
        }

        $builder->add(
            'view_type',
            'choice',
            array(
                'label' => 'block.view_type',
                'choices' => $choices,
                'choices_as_values' => true,
                'property_path' => 'viewType',
                // 'choice_value' is needed here since in Symfony 2.7
                // using the form with NON DEPRECATED 'choices_as_values'
                // is broken.
                // See: https://github.com/symfony/symfony/issues/14377
                'choice_value' => function ($choice) {
                    return $choice;
                },
            )
        );
    }

    protected function addBlockNameForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            'text',
            array(
                'label' => 'block.name',
                'property_path' => 'name',
                // null and empty string have different meanings for name
                // so we set the default value to a single space (instead of
                // an empty string) because of
                // https://github.com/symfony/symfony/issues/5906
                'empty_data' => ' ',
            )
        );
    }

    protected function addParametersForm(FormBuilderInterface $builder, array $options, array $parameterNames = array())
    {
        /** @var \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $options['blockDefinition'];
        $blockDefinitionParameters = $blockDefinition->getParameters();

        if (empty($parameterNames)) {
            $parameterNames = array_keys($blockDefinitionParameters);
        }

        // We're grouping block parameters so they don't conflict with forms from block itself
        $parameterBuilder = $builder->create(
            'parameters',
            'form',
            array(
                'label' => 'block.parameters',
                'inherit_data' => true,
            )
        );

        foreach ($parameterNames as $parameterName) {
            $this->parameterFormMapper->mapParameter(
                $parameterBuilder,
                $blockDefinitionParameters[$parameterName],
                $parameterName,
                array(
                    'label_prefix' => 'block.' . $blockDefinition->getIdentifier(),
                    'property_path_prefix' => 'parameters',
                )
            );
        }

        $builder->add($parameterBuilder);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     *
     * @deprecated Deprecated since Symfony 2.8, to be removed in Symfony 3.0.
     *             Implemented in order not to trigger deprecation notices in Symfony <= 2.7
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
