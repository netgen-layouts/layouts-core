<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class BlockDefinitionValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     */
    public function __construct(BlockDefinitionRegistryInterface $blockDefinitionRegistry)
    {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var \Netgen\BlockManager\Validator\Constraint\BlockDefinition $constraint */
        if (!$this->blockDefinitionRegistry->hasBlockDefinition($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%definitionIdentifier%', $value)
                ->addViolation();
        }
    }
}
