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
        $layoutTypes = $this->configuration->getParameter('layout_types');

        if (!isset($layoutTypes[$constraint->layoutType])) {
            $this->context->buildViolation($constraint->layoutMissingMessage)
                ->setParameter('%layoutType%', $constraint->layoutType)
                ->addViolation();

            return;
        }

        if (!is_array($value)) {
            $this->context->buildViolation($constraint->zonesInvalidMessage)
                ->addViolation();

            return;
        }

        foreach ($value as $zoneIdentifier) {
            if (!isset($layoutTypes[$constraint->layoutType]['zones'][$zoneIdentifier])) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%zoneIdentifier%', $zoneIdentifier)
                    ->addViolation();
            }
        }

        foreach ($layoutTypes[$constraint->layoutType]['zones'] as $zoneIdentifier => $zone) {
            if (!in_array($zoneIdentifier, $value)) {
                $this->context->buildViolation($constraint->zoneMissingMessage)
                    ->setParameter('%zoneIdentifier%', $zoneIdentifier)
                    ->addViolation();
            }
        }
    }
}
