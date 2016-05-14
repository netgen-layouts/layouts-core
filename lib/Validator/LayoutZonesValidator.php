<?php

namespace Netgen\BlockManager\Validator;

use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class LayoutZonesValidator extends ConstraintValidator
{
    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry
     */
    protected $layoutTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry $layoutTypeRegistry
     */
    public function __construct(LayoutTypeRegistry $layoutTypeRegistry)
    {
        $this->layoutTypeRegistry = $layoutTypeRegistry;
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

        if (!$this->layoutTypeRegistry->hasLayoutType($constraint->layoutType)) {
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

        $layoutType = $this->layoutTypeRegistry->getLayoutType($constraint->layoutType);

        foreach ($value as $zoneIdentifier) {
            if (!$layoutType->hasZone($zoneIdentifier)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%zoneIdentifier%', $zoneIdentifier)
                    ->addViolation();
            }
        }

        foreach ($layoutType->getZones() as $zoneIdentifier => $zone) {
            if (!in_array($zoneIdentifier, $value)) {
                $this->context->buildViolation($constraint->zoneMissingMessage)
                    ->setParameter('%zoneIdentifier%', $zoneIdentifier)
                    ->addViolation();
            }
        }
    }
}
