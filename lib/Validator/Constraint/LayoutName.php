<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class LayoutName extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        /**
         * If specified, layout with this UUID will be excluded from the check.
         */
        public ?Uuid $excludedLayoutId = null,
        public string $message = 'netgen_layouts.layout_name.layout_exists',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_layout_name';
    }
}
