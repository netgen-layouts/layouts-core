<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;

class ParametersValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface
     */
    protected $parameterFilterRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface $parameterFilterRegistry
     */
    public function __construct(ParameterFilterRegistryInterface $parameterFilterRegistry)
    {
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
        /** @var \Netgen\BlockManager\Validator\Constraint\Parameters $constraint */
        /** @var \Netgen\BlockManager\Parameters\ParameterCollectionInterface $value */
        $this->filterParameters($value, $constraint->parameters);

        /** @var \Symfony\Component\Validator\Validator\ValidatorInterface $validator */
        $validator = $this->context->getValidator();

        $violations = $validator->validate(
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

        if ($violations->count() > 0) {
            $violation = $violations->offsetGet(0);

            $this->context->buildViolation($constraint->message)
                ->setParameter('%parameterName%', $violation->getPropertyPath())
                ->setParameter('%message%', $violation->getMessage())
                ->addViolation();
        }
    }

    /**
     * Filters the parameter values.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     */
    protected function filterParameters(ParameterCollectionInterface $parameterCollection, array $parameters)
    {
        foreach ($parameterCollection->getParameters() as $parameterName => $parameterValue) {
            if (!isset($parameters[$parameterName])) {
                continue;
            }

            $filters = $this->parameterFilterRegistry->getParameterFilters($parameters[$parameterName]->getType());
            foreach ($filters as $filter) {
                $parameterValue = $filter->filter($parameterValue);
            }

            $parameterCollection->setParameter($parameterName, $parameterValue);
        }
    }

    /**
     * Builds the "fields" array from provided parameters and parameter values.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface $parameterCollection
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     * @param bool $isRequired
     *
     * @return array
     */
    protected function buildConstraintFields(ParameterCollectionInterface $parameterCollection, array $parameters, $isRequired = true)
    {
        $fields = array();
        foreach ($parameters as $parameterName => $parameter) {
            $fields[$parameterName] = $this->buildFieldConstraint($parameter->getConstraints(), $isRequired);

            if ($parameter instanceof CompoundParameterInterface) {
                foreach ($parameter->getParameters() as $subParameterName => $subParameter) {
                    $constraints = $subParameter->getValueConstraints();

                    if (
                        $parameterCollection->hasParameter($parameterName) &&
                        $parameterCollection->getParameter($parameterName) &&
                        $subParameter->isRequired()
                    ) {
                        $constraints = array_merge(
                            $constraints,
                            $subParameter->getRequiredConstraints()
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
