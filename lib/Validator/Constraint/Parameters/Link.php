<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\Parameters;

use Symfony\Component\Validator\Constraint;

final class Link extends Constraint
{
    /**
     * If true, link value cannot be empty.
     */
    public bool $required = false;

    /**
     * If not empty, will limit valid value types to the specified list.
     *
     * @var string[]
     */
    public array $valueTypes = [];

    /**
     * If set to true, the constraint will accept values for invalid or non existing items
     * when using internal link type.
     */
    public bool $allowInvalidInternal = false;

    public function validatedBy(): string
    {
        return 'nglayouts_link';
    }
}
