<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Parameters\CompoundParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
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

        $this->filterParameters($value, $constraint->parameterCollection);

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
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     */
    private function filterParameters(ParameterStruct $parameterStruct, ParameterCollectionInterface $parameterCollection)
    {
        foreach ($parameterStruct->getParameterValues() as $parameterName => $parameterValue) {
            if (!$parameterCollection->hasParameterDefinition($parameterName)) {
                continue;
            }

            $filters = $this->parameterFilterRegistry->getParameterFilters(
                $parameterCollection->getParameterDefinition($parameterName)->getType()->getIdentifier()
            );

            foreach ($filters as $filter) {
                $parameterValue = $filter->filter($parameterValue);
            }

            $parameterStruct->setParameterValue($parameterName, $parameterValue);
        }
    }

    /**
     * Builds the "fields" array from provided parameters and parameter values.
     *
     * @param \Netgen\BlockManager\API\Values\ParameterStruct $parameterStruct
     * @param \Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct $constraint
     *
     * @return array
     */
    private function buildConstraintFields(ParameterStruct $parameterStruct, ParameterStructConstraint $constraint)
    {
        $fields = [];
        foreach ($constraint->parameterCollection->getParameterDefinitions() as $parameterDefinition) {
            $parameterName = $parameterDefinition->getName();
            $parameterValue = $parameterStruct->hasParameterValue($parameterName) ?
                $parameterStruct->getParameterValue($parameterName) :
                null;

            $constraints = $parameterDefinition->getType()->getConstraints($parameterDefinition, $parameterValue);
            if (!$parameterDefinition->isRequired()) {
                $constraints = new Constraints\Optional($constraints);
            }

            $fields[$parameterName] = $constraints;

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                foreach ($parameterDefinition->getParameterDefinitions() as $subParameterDefinition) {
                    $subParameterName = $subParameterDefinition->getName();
                    $subParameterValue = $parameterStruct->hasParameterValue($subParameterName) ?
                        $parameterStruct->getParameterValue($subParameterName) :
                        null;

                    // Sub parameter values are always optional (either missing or set to null)

                    $constraints = [];
                    if ($subParameterValue !== null) {
                        $constraints = $subParameterDefinition->getType()->getConstraints(
                            $subParameterDefinition,
                            $subParameterValue
                        );
                    }

                    $fields[$subParameterName] = new Constraints\Optional($constraints);
                }
            }
        }

        return $fields;
    }
}
