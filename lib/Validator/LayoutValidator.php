<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class LayoutValidator extends ConstraintValidator
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
        /** @var \Netgen\BlockManager\Validator\Constraint\Layout $constraint */
        $layoutConfig = $this->configuration->getParameter('layouts');

        if (!isset($layoutConfig[$value])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%identifier%', $value)
                ->addViolation();
        }
    }
}
