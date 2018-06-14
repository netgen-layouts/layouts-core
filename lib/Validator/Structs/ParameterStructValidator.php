<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\Structs;

use Closure;
use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct as ParameterStructConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates the parameters stored inside the value
 * implementing ParameterStruct interface.
 */
final class ParameterStructValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface
     */
    private $parameterFilterRegistry;

    public function __construct(ParameterFilterRegistryInterface $parameterFilterRegistry)
    {
        $this->parameterFilterRegistry = $parameterFilterRegistry;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ParameterStructConstraint) {
            throw new UnexpectedTypeException($constraint, ParameterStructConstraint::class);
        }

        if (!$value instanceof ParameterStruct) {
            throw new UnexpectedTypeException($value, ParameterStruct::class);
        }

        $this->filterParameters($value, $constraint->parameterDefinitions);

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        // First we validate the value format with constraints coming from the parameter type
        $validator->validate(
            $value->getParameterValues(),
            new Constraints\Collection(
                [
                    'fields' => $this->buildConstraintFields($value, $constraint),
                    'allowMissingFields' => $constraint->allowMissingFields,
                ]
            )
        );

        // Then we validate with runtime constraints coming from parameter definition
        // allowing for validation of values dependent on other parameter struct values
        foreach ($constraint->parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $validator->atPath('[' . $parameterDefinition->getName() . ']')->validate(
                $value->getParameterValue($parameterDefinition->getName()),
                $this->getRuntimeParameterConstraints(
                    $constraint->parameterDefinitions,
                    $parameterDefinition,
                    $value
                )
            );
        }
    }

    /**
     * Filters the parameter values.
     */
    private function filterParameters(
        ParameterStruct $parameterStruct,
        ParameterDefinitionCollectionInterface $parameterDefinitions
    ): void {
        foreach ($parameterStruct->getParameterValues() as $parameterName => $parameterValue) {
            if (!$parameterDefinitions->hasParameterDefinition($parameterName)) {
                continue;
            }

            $filters = $this->parameterFilterRegistry->getParameterFilters(
                $parameterDefinitions->getParameterDefinition($parameterName)->getType()->getIdentifier()
            );

            foreach ($filters as $filter) {
                $parameterValue = $filter->filter($parameterValue);
            }

            $parameterStruct->setParameterValue($parameterName, $parameterValue);
        }
    }

    /**
     * Builds the "fields" array of the Collection constraint from provided parameters
     * and parameter values.
     */
    private function buildConstraintFields(
        ParameterStruct $parameterStruct,
        ParameterStructConstraint $constraint
    ): array {
        $fields = [];

        foreach ($constraint->parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $constraints = $this->getParameterConstraints($parameterDefinition, $parameterStruct);

            if (!$parameterDefinition->isRequired()) {
                $constraints = new Constraints\Optional($constraints);
            }

            $fields[$parameterDefinition->getName()] = $constraints;

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                foreach ($parameterDefinition->getParameterDefinitions() as $subParameterDefinition) {
                    $fields[$subParameterDefinition->getName()] = new Constraints\Optional(
                        // Sub parameter values are always optional (either missing or set to null)
                        // so we don't have to validate empty values
                        $this->getParameterConstraints($subParameterDefinition, $parameterStruct, false)
                    );
                }
            }
        }

        return $fields;
    }

    /**
     * Returns all constraints applied on a parameter coming directly from parameter type.
     *
     * If $validateEmptyValue is false, values equal to null will not be validated
     * and will simply return an empty array of constraints.
     */
    private function getParameterConstraints(
        ParameterDefinition $parameterDefinition,
        ParameterStruct $parameterStruct,
        bool $validateEmptyValue = true
    ): array {
        $parameterValue = $parameterStruct->getParameterValue(
            $parameterDefinition->getName()
        );

        if (!$validateEmptyValue && $parameterValue === null) {
            return [];
        }

        return $parameterDefinition->getType()->getConstraints(
            $parameterDefinition,
            $parameterValue
        );
    }

    /**
     * Returns all constraints applied on a parameter coming from the parameter definition.
     */
    private function getRuntimeParameterConstraints(
        ParameterDefinitionCollectionInterface $parameterDefinitions,
        ParameterDefinition $parameterDefinition,
        ParameterStruct $parameterStruct
    ): array {
        $constraints = [];

        foreach ($parameterDefinition->getConstraints() as $constraint) {
            if ($constraint instanceof Closure) {
                $constraint = $constraint(
                    $parameterStruct->getParameterValue($parameterDefinition->getName()),
                    $this->getParameterValues($parameterDefinitions, $parameterStruct),
                    $parameterDefinition
                );
            }

            if ($constraint instanceof Constraint) {
                $constraints[] = $constraint;
            }
        }

        return $constraints;
    }

    private function getParameterValues(
        ParameterDefinitionCollectionInterface $parameterDefinitions,
        ParameterStruct $parameterStruct
    ): array {
        $emptyValues = [];

        foreach ($parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $emptyValues[$parameterDefinition->getName()] = null;

            if ($parameterDefinition instanceof CompoundParameterDefinition) {
                foreach ($parameterDefinition->getParameterDefinitions() as $subParameterDefinition) {
                    $emptyValues[$subParameterDefinition->getName()] = null;
                }
            }
        }

        return $parameterStruct->getParameterValues() + $emptyValues;
    }
}
