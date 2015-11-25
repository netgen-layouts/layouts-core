<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

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

        $blockDefinitionParameters = $blockDefinition->getParameters();
        $parameterConstraints = $blockDefinition->getParameterConstraints();

        foreach ($value as $parameterName => $parameterValue) {
            if (!isset($blockDefinitionParameters[$parameterName])) {
                $this->context->buildViolation($constraint->excessParameterMessage)
                    ->setParameter('%parameter%', $parameterName)
                    ->setInvalidValue($parameterValue)
                    ->atPath('parameters.' . $parameterName)
                    ->addViolation();
            }
        }

        foreach ($blockDefinitionParameters as $parameterName => $parameter) {
            if (!is_array($parameterConstraints[$parameterName])) {
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
                $parameterConstraints[$parameterName]
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
