<?php

namespace Netgen\BlockManager\Form\Type;

use Netgen\BlockManager\API\Values\BlockUpdateStruct as APIBlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Form\FormData;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use RuntimeException;

class BlockType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formData = $this->validateFormData($options["data"]);

        /** @var \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface $formData[definition] */

        // We're grouping block parameters so they don't conflict with forms from block itself
        $parameterBuilder = $builder->create('parameters', 'form', array('label' => 'Parameters', 'inherit_data' => true));

        foreach ($formData->definition->getParameters() as $blockParameter) {
            $parameterBuilder->add(
                $blockParameter->getIdentifier(),
                $blockParameter->getFormType(),
                array(
                    'label' => $blockParameter->getName(),
                    'property_path' => 'payload.parameters[' . $blockParameter->getIdentifier() . ']'
                ) + $blockParameter->mapFormTypeOptions()
            );
        }

        $builder->add($parameterBuilder);

        $blockConfig = $this->configuration->getBlockConfig(
            $formData->target->getDefinitionIdentifier()
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
                'property_path' => 'payload.viewType'
            )
        );
    }

    /**
     * Validates received form data
     *
     * @param \Netgen\BlockManager\Form\FormData $formData
     *
     * @return \Netgen\BlockManager\Form\FormData
     */
    protected function validateFormData($formData)
    {
        if (!$formData instanceof FormData) {
            throw new RuntimeException(
                'Form data must be an instance of FormData class.'
            );
        }

        if (!$formData->definition instanceof BlockDefinitionInterface) {
            throw new RuntimeException(
                'Form data definition must be an instance of BlockDefinitionInterface.'
            );
        }

        if (!$formData->target instanceof Block) {
            throw new RuntimeException(
                'Form data definition must be an instance of Block interface.'
            );
        }

        if (!$formData->payload instanceof APIBlockUpdateStruct) {
            $formData->payload = new BlockUpdateStruct();
            $formData->payload->setParameters($formData->target->getParameters());
            $formData->payload->viewType = $formData->target->getViewType();
        }

        return $formData;
    }
}
