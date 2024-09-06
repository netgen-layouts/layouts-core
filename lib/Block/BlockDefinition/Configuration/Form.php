<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Utils\HydratorTrait;

final class Form
{
    use HydratorTrait;

    private string $identifier;

    /**
     * @var class-string<\Symfony\Component\Form\FormTypeInterface>
     */
    private string $type;

    /**
     * Returns the form identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the form type.
     *
     * @return class-string<\Symfony\Component\Form\FormTypeInterface>
     */
    public function getType(): string
    {
        return $this->type;
    }
}
