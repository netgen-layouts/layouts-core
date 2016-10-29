<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Parameters\CompoundParameterDefinitionInterface;
use Netgen\BlockManager\API\Values\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\Parameters;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ParametersValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $parameterTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface
     */
    protected $parameterFilterRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface $parameterTypeRegistry
     * @param \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface $parameterFilterRegistry
     */
    public function __construct(
        ParameterTypeRegistryInterface $parameterTypeRegistry,
        ParameterFilterRegistryInterface $parameterFilterRegistry
    ) {
        $this->parameterTypeRegistry = $parameterTypeRegistry;
        $this->parameterFilterRegistry = $parameterFilterRegistry;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Parameters) {
            throw new UnexpectedTypeException($constraint, Parameters::class);
        }

        if (!$value instanceof ParameterCollectionInterface) {
            throw new UnexpectedTypeException($value, ParameterCollectionInterface::class);
        }

        $this->filterParameters($value, $constraint->parameters);

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);

        $validator->validate(
            $value->getParameters(),
            new Constraints\Collection(
                array(
                    'fields' => $this->buildConstraintFields(
                        $value,
                        $constraint->parameters,
                        $constraint->required
                    ),
                )
            )
        );
    }

    /**
     * Filters the parameter values.
     *
     * @param \Netgen\BlockManager\API\Values\ParameterCollectionInterface $parameterCollection
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[] $parameterDefinitions
     */
    protected function filterParameters(ParameterCollectionInterface $parameterCollection, array $parameterDefinitions)
    {
        foreach ($parameterCollection->getParameters() as $parameterName => $parameter) {
            if (!isset($parameterDefinitions[$parameterName])) {
                continue;
            }

            $filters = $this->parameterFilterRegistry->getParameterFilters($parameterDefinitions[$parameterName]->getType());
            foreach ($filters as $filter) {
                $parameter = $filter->filter($parameter);
            }

            $parameterCollection->setParameter($parameterName, $parameter);
        }
    }

    /**
     * Builds the "fields" array from provided parameters and parameter values.
     *
     * @param \Netgen\BlockManager\API\Values\ParameterCollectionInterface $parameterCollection
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[] $parameterDefinitions
     * @param bool $isRequired
     *
     * @return array
     */
    protected function buildConstraintFields(ParameterCollectionInterface $parameterCollection, array $parameterDefinitions, $isRequired = true)
    {
        $fields = array();
        foreach ($parameterDefinitions as $parameterName => $parameterDefinition) {
            $parameterValue = $parameterCollection->hasParameter($parameterName) ?
                $parameterCollection->getParameter($parameterName) :
                null;

            $parameterType = $this->parameterTypeRegistry->getParameterType($parameterDefinition->getType());

            $fields[$parameterName] = $this->buildFieldConstraint(
                $parameterType->getConstraints($parameterDefinition, $parameterValue),
                $isRequired
            );

            if ($parameterDefinition instanceof CompoundParameterDefinitionInterface) {
                foreach ($parameterDefinition->getParameters() as $subParameterName => $subParameterDefinition) {
                    $subParameterValue = $parameterCollection->hasParameter($subParameterName) ?
                        $parameterCollection->getParameter($subParameterName) :
                        null;

                    $subParameterType = $this->parameterTypeRegistry->getParameterType($subParameterDefinition->getType());
                    $constraints = $subParameterType->getValueConstraints($subParameterDefinition, $subParameterValue);

                    if (
                        $parameterCollection->hasParameter($parameterName) &&
                        $parameterCollection->getParameter($parameterName) &&
                        $subParameterDefinition->isRequired()
                    ) {
                        $constraints = array_merge(
                            $constraints,
                            $subParameterType->getRequiredConstraints($subParameterDefinition, $subParameterValue)
                        );
                    }

                    $fields[$subParameterName] = $this->buildFieldConstraint($constraints, $isRequired);
                }
            }
        }

        return $fields;
    }

    /**
     * Builds the Constraints\Required or Constraints\Optional constraint as specified by $isRequired flag.
     *
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     * @param bool $isRequired
     *
     * @return \Symfony\Component\Validator\Constraint
     */
    protected function buildFieldConstraint(array $constraints, $isRequired)
    {
        return $isRequired ?
            new Constraints\Required($constraints) :
            new Constraints\Optional($constraints);
    }
}
