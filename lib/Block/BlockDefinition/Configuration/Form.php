<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Utils\HydratorTrait;

final class Form
{
    use HydratorTrait;

    /**
     * Returns the form identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns the form type.
     *
     * @var class-string<\Symfony\Component\Form\FormTypeInterface>
     */
    public private(set) string $type;
}
