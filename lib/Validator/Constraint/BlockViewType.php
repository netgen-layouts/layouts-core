<?php

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class BlockViewType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'View type "%viewType%" does not exist.';

    /**
     * @var string
     */
    public $definitionIdentifierMissingMessage = 'Block definition "%definitionIdentifier%" does not exist.';

    /**
     * @var string
     */
    public $definitionIdentifier;

    /**
     * Returns the name of the class that validates this constraint.
     *
     * @return string
     */
    public function validatedBy()
    {
        return 'ngbm_block_view_type';
    }
}
