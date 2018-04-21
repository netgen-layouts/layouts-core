<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\CompoundParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
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

    public function validate($value, Constraint $constraint)
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

        $validator->validate(
            $value->getParameterValues(),
            new Constraints\Collection(
                [
                    'fields' => $this->buildConstraintFields($value, $constraint),
                    'allowMissingFields' => $constraint->allowMissingFields,
                ]
            )
        );
    }

    /**
     * Filters the parameter values.
     *
     * @param \Netgen\BlockManager\API\Values\ParameterStruct $parameterStruct
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface $parameterDefinitions
     */
    private function filterParameters(
        ParameterStruct $parameterStruct,
        ParameterDefinitionCollectionInterface $parameterDefinitions
    ) {
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
     *
     * @param \Netgen\BlockManager\API\Values\ParameterStruct $parameterStruct
     * @param \Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct $constraint
     *
     * @return array
     */
    private function buildConstraintFields(
        ParameterStruct $parameterStruct,
        ParameterStructConstraint $constraint
    ) {
        $fields = [];

        foreach ($constraint->parameterDefinitions->getParameterDefinitions() as $parameterDefinition) {
            $constraints = $this->getParameterConstraints($parameterStruct, $parameterDefinition);

            if (!$parameterDefinition->isRequired()) {
                $constraints = new Constraints\Optional($constraints);
            }

            $fields[$parameterDefinition->getName()] = $constraints;

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                foreach ($parameterDefinition->getParameterDefinitions() as $subParameterDefinition) {
                    $fields[$subParameterDefinition->getName()] = new Constraints\Optional(
                        // Sub parameter values are always optional (either missing or set to null)
                        // so we don't have to validate empty values
                        $this->getParameterConstraints($parameterStruct, $subParameterDefinition, false)
                    );
                }
            }
        }

        return $fields;
    }

    /**
     * Returns all constraints applied on a parameter.
     *
     * If $validateEmptyValue is false, values equal to null will not be validated
     * and will simply return an empty array of constraints.
     *
     * @param \Netgen\BlockManager\API\Values\ParameterStruct $parameterStruct
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param bool $validateEmptyValue
     *
     * @return array
     */
    private function getParameterConstraints(
        ParameterStruct $parameterStruct,
        ParameterDefinitionInterface $parameterDefinition,
        $validateEmptyValue = true
    ) {
        $parameterName = $parameterDefinition->getName();
        $parameterValue = $parameterStruct->hasParameterValue($parameterName) ?
            $parameterStruct->getParameterValue($parameterName) :
            null;

        if (!$validateEmptyValue && $parameterValue === null) {
            return [];
        }

        return $parameterDefinition->getType()->getConstraints(
            $parameterDefinition,
            $parameterValue
        );
    }
}
