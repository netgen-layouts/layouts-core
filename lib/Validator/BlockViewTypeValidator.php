<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class BlockViewTypeValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var \Netgen\BlockManager\Validator\Constraint\BlockViewType $constraint */
        $blockConfig = $this->configuration->getParameter('block_definitions');

        if (!isset($blockConfig[$constraint->definitionIdentifier])) {
            $this->context->buildViolation($constraint->definitionIdentifierMissingMessage)
                ->setParameter('%definitionIdentifier%', $constraint->definitionIdentifier)
                ->addViolation();

            return;
        }

        if (!isset($blockConfig[$constraint->definitionIdentifier]['view_types'][$value])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%viewType%', $value)
                ->addViolation();
        }
    }
}
