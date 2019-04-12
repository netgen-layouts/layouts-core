<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class LayoutName extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_layouts.layout_name.layout_exists';

    /**
     * If specified, layout with this ID will be excluded from the check.
     *
     * @var int|null
     */
    public $excludedLayoutId;

    public function validatedBy(): string
    {
        return 'nglayouts_layout_name';
    }
}
