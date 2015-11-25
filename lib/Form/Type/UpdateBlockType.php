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

        $blockConfig = $this->configuration->getBlockConfig(
            $blockDefinition->getIdentifier()
        );

        $choices = array();
        foreach ($blockConfig['view_types'] as $identifier => $viewType) {
            $choices[$identifier] = $viewType['name'];
        }

        $builder->add(
            'view_type',
            'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
            array(
                'label' => 'View type',
                'choices' => $choices,
                'property_path' => 'updateStruct.viewType',
            )
        );

        $builder->add(
            'name',
            'Symfony\Component\Form\Extension\Core\Type\TextType',
            array(
                'label' => 'Name',
                'property_path' => 'updateStruct.name',
                // null and empty string have different meanings for name
                // so we set the default value to a single space (instead of
                // an empty string) because of
                // https://github.com/symfony/symfony/issues/5906
                'empty_data' => ' ',
            )
        );

        // We're grouping block parameters so they don't conflict with forms from block itself
        $parameterBuilder = $builder->create(
            'parameters',
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            array(
                'label' => 'Parameters',
                'inherit_data' => true
            )
        );

        $parameters = $blockDefinition->getParameters();
        $parameterNames = $blockDefinition->getParameterNames();
        $parameterConstraints = $blockDefinition->getParameterConstraints();

        foreach ($parameters as $parameterIdentifier => $blockParameter) {
            $parameterBuilder->add(
                $parameterIdentifier,
                $blockParameter->getFormType(),
                array(
                    'label' => isset($parameterNames[$parameterIdentifier]) ?
                        $parameterNames[$parameterIdentifier] :
                        null,
                    'property_path' => 'updateStruct.parameters[' . $parameterIdentifier . ']',
                    'constraints' => $parameterConstraints[$parameterIdentifier] !== false ?
                        $parameterConstraints[$parameterIdentifier] :
                        null,
                ) + $blockParameter->mapFormTypeOptions()
            );
        }

        $builder->add($parameterBuilder);
    }
}
