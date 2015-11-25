<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class LayoutZones extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Layout zone "%zoneIdentifier%" does not exist.';

    /**
     * @var string
     */
    public $zonesInvalidMessage = 'Zone identifiers are not an array.';

    /**
     * @var string
     */
    public $layoutMissingMessage = 'Layout "%layoutIdentifier%" does not exist.';

    /**
     * @var string
     */
    public $layoutIdentifier;

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_layout_zones';
    }
}
