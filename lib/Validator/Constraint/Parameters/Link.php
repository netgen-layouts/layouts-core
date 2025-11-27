<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\Parameters;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class Link extends Constraint
{
    /**
     * @param string[] $valueTypes
     */
    #[HasNamedArguments]
    public function __construct(
        /**
         * If true, link value cannot be empty.
         */
        public bool $isRequired = false,
        /**
         * If not empty, will limit valid value types to the specified list.
         */
        public array $valueTypes = [],
        /**
         * If set to true, the constraint will accept values for invalid or non existing items
         * when using internal link type.
         */
        public bool $allowInvalidInternal = false,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_link';
    }
}
