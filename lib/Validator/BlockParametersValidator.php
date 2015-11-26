<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\BlockDefinition\Parameter;
use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use RuntimeException;

class BlockParametersValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     */
    public function __construct(
        BlockDefinitionRegistryInterface $blockDefinitionRegistry,
        ValidatorInterface $validator
    ) {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->validator = $validator;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var \Netgen\BlockManager\Validator\Constraint\BlockParameters $constraint */
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $constraint->definitionIdentifier
        );

        $parameters = $blockDefinition->getParameters();
        $constraints = $blockDefinition->getParameterConstraints();

        foreach ($value as $parameterName => $parameterValue) {
            if (!isset($parameters[$parameterName])) {
                $this->context->buildViolation($constraint->excessParameterMessage)
                    ->setParameter('%parameter%', $parameterName)
                    ->setInvalidValue($parameterValue)
                    ->atPath('parameters.' . $parameterName)
                    ->addViolation();
            }
        }

        foreach ($parameters as $parameterName => $parameter) {
            if (!isset($constraints[$parameterName]) || !is_array($constraints[$parameterName])) {
                continue;
            }

            if (!isset($value[$parameterName])) {
                $this->context->buildViolation($constraint->missingParameterMessage)
                    ->setParameter('%parameter%', $parameterName)
                    ->setInvalidValue(null)
                    ->atPath('parameters.' . $parameterName)
                    ->addViolation();

                continue;
            }

            $violations = $this->validator->validate(
                $value[$parameterName],
                $constraints[$parameterName]
            );

            foreach ($violations as $violation) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%whatIsWrong%', $violation->getMessage())
                    ->setInvalidValue($value[$parameterName])
                    ->atPath('parameters.' . $parameterName)
                    ->addViolation();
            }
        }
    }
}
