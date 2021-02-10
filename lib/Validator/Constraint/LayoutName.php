<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraint;

final class LayoutName extends Constraint
{
    public string $message = 'netgen_layouts.layout_name.layout_exists';

    /**
     * If specified, layout with this UUID will be excluded from the check.
     */
    public ?UuidInterface $excludedLayoutId = null;

    public function validatedBy(): string
    {
        return 'nglayouts_layout_name';
    }
}
