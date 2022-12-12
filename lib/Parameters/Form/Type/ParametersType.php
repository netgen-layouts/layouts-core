<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Type;

use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\Form\MapperInterface;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_intersect;
use function count;

final class ParametersType extends AbstractType
{
    private ContainerInterface $mappers;

    public function __construct(ContainerInterface $mappers)
    {
        $this->mappers = $mappers;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts');

        $resolver->setRequired(
            [
                'groups',
                'parameter_definitions',
                'label_prefix',
            ],
        );

        $resolver->setAllowedTypes('data', ParameterStruct::class);
        $resolver->setAllowedTypes('parameter_definitions', ParameterDefinitionCollectionInterface::class);
        $resolver->setAllowedTypes('label_prefix', 'string');
        $resolver->setAllowedTypes('groups', 'string[]');

        $resolver->setDefault('groups', []);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions */
        $parameterDefinitions = $options['parameter_definitions'];

        foreach ($parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            if (!$this->includeParameter($parameterDefinition, $options['groups'])) {
                continue;
            }

            $parameterName = $parameterDefinition->getName();
            $parameterLabel = $parameterDefinition->getLabel();

            $mapper = $this->getMapper($parameterDefinition->getType()::getIdentifier());

            $defaultOptions = [
                'label' => $parameterLabel ?? $options['label_prefix'] . '.' . $parameterName,
                'translation_domain' => 'nglayouts',
                'property_path' => 'parameterValues[' . $parameterName . ']',
                'ngl_parameter_definition' => $parameterDefinition,
            ];

            $parameterForm = $builder->create(
                $parameterName,
                $mapper->getFormType(),
                $mapper->mapOptions(
                    $parameterDefinition,
                ) + $defaultOptions,
            );

            $mapper->handleForm($parameterForm, $parameterDefinition);

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                $this->buildForm(
                    $parameterForm,
                    [
                        'parameter_definitions' => $parameterDefinition,
                    ] + $options,
                );
            }

            $builder->add($parameterForm);
        }
    }

    /**
     * Returns if the parameter will be included in the form based on provided groups.
     *
     * @param string[] $groups
     */
    private function includeParameter(ParameterDefinition $parameterDefinition, array $groups): bool
    {
        $parameterGroups = $parameterDefinition->getGroups();

        if (count($parameterGroups) === 0 || count($groups) === 0) {
            return true;
        }

        return count(array_intersect($parameterGroups, $groups)) > 0;
    }

    /**
     * Returns the mapper for provided parameter type from the collection.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterTypeException If the mapper does not exist or is not of correct type
     */
    private function getMapper(string $parameterType): MapperInterface
    {
        if (!$this->mappers->has($parameterType)) {
            throw ParameterTypeException::noFormMapper($parameterType);
        }

        $mapper = $this->mappers->get($parameterType);
        if (!$mapper instanceof MapperInterface) {
            throw ParameterTypeException::noFormMapper($parameterType);
        }

        return $mapper;
    }
}
