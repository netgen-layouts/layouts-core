<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Type;

use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Netgen\Layouts\Form\AbstractType;
use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\Form\MapperInterface;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ParametersType extends AbstractType
{
    /**
     * @var \Netgen\Layouts\Parameters\Form\MapperInterface[]
     */
    private $mappers;

    /**
     * @param \Netgen\Layouts\Parameters\Form\MapperInterface[] $mappers
     */
    public function __construct(array $mappers)
    {
        $this->mappers = array_filter(
            $mappers,
            static function (MapperInterface $mapper): bool {
                return true;
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(
            [
                'groups',
                'parameter_definitions',
                'label_prefix',
            ]
        );

        $resolver->setAllowedTypes('data', ParameterStruct::class);
        $resolver->setAllowedTypes('parameter_definitions', ParameterDefinitionCollectionInterface::class);
        $resolver->setAllowedTypes('label_prefix', 'string');
        $resolver->setAllowedTypes('groups', 'string[]');

        $resolver->setDefault('translation_domain', 'ngbm');
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
            $parameterType = $parameterDefinition->getType()::getIdentifier();

            if (!isset($this->mappers[$parameterType])) {
                throw ParameterTypeException::noFormMapper($parameterType);
            }

            $mapper = $this->mappers[$parameterType];

            $defaultOptions = [
                'label' => $parameterLabel ?? $options['label_prefix'] . '.' . $parameterName,
                'translation_domain' => 'ngbm',
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

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                $this->buildForm(
                    $parameterForm,
                    [
                        'parameter_definitions' => $parameterDefinition,
                    ] + $options
                );
            }

            $builder->add($parameterForm);
        }
    }

    /**
     * Returns if the parameter will be included in the form based on provided groups.
     */
    private function includeParameter(ParameterDefinition $parameterDefinition, array $groups): bool
    {
        $parameterGroups = $parameterDefinition->getGroups();

        if (count($parameterGroups) === 0 || count($groups) === 0) {
            return true;
        }

        return count(array_intersect($parameterGroups, $groups)) > 0;
    }
}
