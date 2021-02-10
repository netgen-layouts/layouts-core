<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\Parameters;

use Symfony\Component\Validator\Constraint;

final class ItemLink extends Constraint
{
    public string $message = 'netgen_layouts.item_link.no_item';

    public string $invalidItemMessage = 'netgen_layouts.item_link.invalid_item';

    public string $valueTypeNotAllowedMessage = 'netgen_layouts.item_link.value_type_not_allowed';

    /**
     * If set to true, the constraint will accept values for invalid or non existing items.
     */
    public bool $allowInvalid = false;

    /**
     * If not empty, will limit valid value types to the specified list.
     *
     * @var string[]
     */
    public array $valueTypes = [];

    public function validatedBy(): string
    {
        return 'nglayouts_item_link';
    }
}
