<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class LayoutZones extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.layout_zones.no_zone';

    /**
     * @var string
     */
    public $zonesInvalidMessage = 'netgen_block_manager.layout_zones.invalid_zone';

    /**
     * @var string
     */
    public $zoneMissingMessage = 'netgen_block_manager.layout_zones.zone_missing';

    /**
     * @var string
     */
    public $layoutMissingMessage = 'netgen_block_manager.layout_zones.no_layout_type';

    /**
     * @var string
     */
    public $layoutType;

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
