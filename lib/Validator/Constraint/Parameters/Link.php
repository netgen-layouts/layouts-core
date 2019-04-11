<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\Parameters;

use Symfony\Component\Validator\Constraint;

final class Link extends Constraint
{
    /**
     * If true, link value cannot be empty.
     *
     * @var bool
     */
    public $required = false;

    /**
     * If not empty, will limit valid value types to the specified list.
     *
     * @var array
     */
    public $valueTypes = [];

    /**
     * If set to true, the constraint will accept values for invalid or non existing items
     * when using internal link type.
     *
     * @var bool
     */
    public $allowInvalidInternal = false;

    public function validatedBy(): string
    {
        return 'ngbm_link';
    }
}
