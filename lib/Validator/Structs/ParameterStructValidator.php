<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Structs;

use Closure;
use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct as ParameterStructConstraint;
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
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ParameterStructConstraint) {
            throw new UnexpectedTypeException($constraint, ParameterStructConstraint::class);
        }

        if (!$value instanceof ParameterStruct) {
            throw new UnexpectedTypeException($value, ParameterStruct::class);
        }

        if ($constraint->checkReadOnlyFields && !$constraint->payload instanceof ParameterCollectionInterface) {
            throw new UnexpectedTypeException($constraint->payload, ParameterCollectionInterface::class);
        }

        $validator = $this->context->getValidator()->inContext($this->context);

        // First we validate the value format with constraints coming from the parameter type
        $validator->validate(
            $value->parameterValues,
            new Constraints\Collection(
                fields: [...$this->buildConstraintFields($value, $constraint)],
                allowMissingFields: $constraint->allowMissingFields,
            ),
        );

        $allParameterValues = [...$this->getAllValues($constraint->parameterDefinitions, $value)];

        // Then we validate with runtime constraints coming from parameter definition
        // allowing for validation of values dependent on other parameter struct values
        foreach ($constraint->parameterDefinitions->parameterDefinitions as $parameterDefinition) {
            $parameterName = $parameterDefinition->name;
            $parameterValue = $value->getParameterValue($parameterName);

            if ($constraint->checkReadOnlyFields && $parameterDefinition->isReadOnly) {
                if (
                    $parameterValue !== null
                    && $parameterValue !== $constraint->payload->getParameter($parameterName)->value
                ) {
                    $this->context->buildViolation($constraint->fieldReadOnlyMessage)
                        ->setParameter('%parameterName%', $parameterDefinition->name)
                        ->addViolation();

                    return;
                }
            }

            $validator->atPath('[' . $parameterDefinition->name . ']')->validate(
                $parameterValue,
                [
                    ...$this->getRuntimeParameterConstraints(
                        $parameterDefinition,
                        $parameterValue,
                        $allParameterValues,
                    ),
                ],
            );
        }
    }

    /**
     * Builds the "fields" array of the Collection constraint from provided parameters
     * and parameter values.
     *
     * @return iterable<string, list<\Symfony\Component\Validator\Constraint>|\Symfony\Component\Validator\Constraint>
     */
    private function buildConstraintFields(
        ParameterStruct $parameterStruct,
        ParameterStructConstraint $constraint,
    ): iterable {
        foreach ($constraint->parameterDefinitions->parameterDefinitions as $parameterDefinition) {
            $constraints = $this->getParameterConstraints($parameterDefinition, $parameterStruct);

            if (!$parameterDefinition->isRequired) {
                $constraints = new Constraints\Optional($constraints);
            }

            yield $parameterDefinition->name => $constraints;

            if ($parameterDefinition->isCompound) {
                foreach ($parameterDefinition->parameterDefinitions as $subParameterDefinition) {
                    yield $subParameterDefinition->name => new Constraints\Optional(
                        // Sub parameter values are always optional (either missing or set to null)
                        // so we don't have to validate empty values
                        $this->getParameterConstraints($subParameterDefinition, $parameterStruct, false),
                    );
                }
            }
        }
    }

    /**
     * Returns all constraints applied on a parameter coming directly from parameter type.
     *
     * If $validateEmptyValue is false, values equal to null will not be validated
     * and will simply return an empty array of constraints.
     *
     * @return list<\Symfony\Component\Validator\Constraint>
     */
    private function getParameterConstraints(
        ParameterDefinition $parameterDefinition,
        ParameterStruct $parameterStruct,
        bool $validateEmptyValue = true,
    ): array {
        $parameterValue = $parameterStruct->getParameterValue(
            $parameterDefinition->name,
        );

        if (!$validateEmptyValue && $parameterValue === null) {
            return [];
        }

        return $parameterDefinition->type->getConstraints(
            $parameterDefinition,
            $parameterValue,
        );
    }

    /**
     * Returns all constraints applied on a parameter coming from the parameter definition.
     *
     * @param array<string, mixed> $allParameterValues
     *
     * @return iterable<\Symfony\Component\Validator\Constraint>
     */
    private function getRuntimeParameterConstraints(
        ParameterDefinition $parameterDefinition,
        mixed $parameterValue,
        array $allParameterValues,
    ): iterable {
        foreach ($parameterDefinition->constraints as $constraint) {
            if ($constraint instanceof Closure) {
                $constraint = $constraint($parameterValue, $allParameterValues, $parameterDefinition);
            }

            yield $constraint;
        }
    }

    /**
     * @return iterable<string, mixed>
     */
    private function getAllValues(
        ParameterDefinitionCollectionInterface $definitions,
        ParameterStruct $parameterStruct,
    ): iterable {
        foreach ($definitions->parameterDefinitions as $parameterDefinition) {
            yield $parameterDefinition->name => null;

            if ($parameterDefinition->isCompound) {
                foreach ($parameterDefinition->parameterDefinitions as $subParameterDefinition) {
                    yield $subParameterDefinition->name => null;
                }
            }
        }

        yield from $parameterStruct->parameterValues;
    }
}
