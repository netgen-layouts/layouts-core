<?php

namespace Netgen\BlockManager\Parameters\Form\Type;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Parameters\CompoundParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\Registry\FormMapperRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ParametersType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\FormMapperRegistryInterface
     */
    private $formMapperRegistry;

    public function __construct(FormMapperRegistryInterface $formMapperRegistry)
    {
        $this->formMapperRegistry = $formMapperRegistry;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(
            [
                'groups',
                'parameter_collection',
                'label_prefix',
            ]
        );

        $resolver->setAllowedTypes('groups', 'array');
        $resolver->setAllowedTypes('data', ParameterStruct::class);
        $resolver->setAllowedTypes('parameter_collection', ParameterCollectionInterface::class);
        $resolver->setAllowedTypes('label_prefix', 'string');

        $resolver->setDefault('groups', []);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection */
        $parameterCollection = $options['parameter_collection'];

        foreach ($parameterCollection->getParameterDefinitions() as $parameterDefinition) {
            if (!$this->includeParameter($parameterDefinition, $options['groups'])) {
                continue;
            }

            $parameterName = $parameterDefinition->getName();
            $parameterLabel = $parameterDefinition->getLabel();

            $mapper = $this->formMapperRegistry->getFormMapper(
                $parameterDefinition->getType()->getIdentifier()
            );

            $defaultOptions = [
                'label' => $parameterLabel === null ?
                    $options['label_prefix'] . '.' . $parameterName :
                    $parameterLabel,
                'property_path' => 'parameterValues[' . $parameterName . ']',
                'ngbm_parameter_definition' => $parameterDefinition,
            ];

            $parameterForm = $builder->create(
                $parameterName,
                $mapper->getFormType(),
                $mapper->mapOptions(
                    $parameterDefinition
                ) + $defaultOptions
            );

            $mapper->handleForm($parameterForm, $parameterDefinition);

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                $this->buildForm(
                    $parameterForm,
                    [
                        'parameter_collection' => $parameterDefinition,
                    ] + $options
                );
            }

            $builder->add($parameterForm);
        }
    }

    /**
     * Returns if the parameter will be included in the form based on provided groups.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param array $groups
     *
     * @return bool
     */
    private function includeParameter(ParameterDefinitionInterface $parameterDefinition, array $groups = [])
    {
        $parameterGroups = $parameterDefinition->getGroups();

        if (empty($parameterGroups) || empty($groups)) {
            return true;
        }

        return !empty(array_intersect($parameterGroups, $groups));
    }
}
