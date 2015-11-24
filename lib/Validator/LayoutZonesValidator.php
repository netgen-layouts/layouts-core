<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class LayoutZonesValidator extends ConstraintValidator
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
        /** @var \Netgen\BlockManager\Validator\Constraint\LayoutZones $constraint */
        $layoutConfig = $this->configuration->getParameter('layouts');

        if (!isset($layoutConfig[$constraint->layoutIdentifier])) {
            $this->context->buildViolation($constraint->layoutMissingMessage)
                ->setParameter('%layoutIdentifier%', $constraint->layoutIdentifier)
                ->addViolation();

            return;
        }

        if (!is_array($value)) {
            return;
        }

        foreach ($value as $zoneIdentifier) {
            if (!isset($layoutConfig[$constraint->layoutIdentifier]['zones'][$zoneIdentifier])) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%zoneIdentifier%', $zoneIdentifier)
                    ->addViolation();
            }
        }
    }
}
