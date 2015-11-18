<?php

namespace Netgen\BlockManager\Form\Type;

use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Form\Data\UpdateBlockData;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use RuntimeException;

class UpdateBlockType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(
        BlockDefinitionRegistryInterface $blockDefinitionRegistry,
        ConfigurationInterface $configuration
    ) {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->configuration = $configuration;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'ngbm_block';
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     *
     * @throws \RuntimeException If form data is not of expected type
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formData = $options['data'];
        if (!$formData instanceof UpdateBlockData) {
            throw new RuntimeException('Form data must be an instance of UpdateBlockData class.');
        }

        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $formData->block->getDefinitionIdentifier()
        );

        // We're grouping block parameters so they don't conflict with forms from block itself
        $parameterBuilder = $builder->create('parameters', 'form', array('label' => 'Parameters', 'inherit_data' => true));

        foreach ($blockDefinition->getParameters() as $blockParameter) {
            $parameterBuilder->add(
                $blockParameter->getIdentifier(),
                $blockParameter->getFormType(),
                array(
                    'label' => $blockParameter->getName(),
                    'property_path' => 'updateStruct.parameters[' . $blockParameter->getIdentifier() . ']',
                ) + $blockParameter->mapFormTypeOptions()
            );
        }

        $builder->add($parameterBuilder);

        $blockConfig = $this->configuration->getBlockConfig(
            $blockDefinition->getIdentifier()
        );

        $choices = array();
        foreach ($blockConfig['view_types'] as $identifier => $viewType) {
            $choices[$identifier] = $viewType['name'];
        }

        $builder->add(
            'view_type',
            'choice',
            array(
                'label' => 'View type',
                'choices' => $choices,
                'property_path' => 'updateStruct.viewType',
            )
        );
    }
}
