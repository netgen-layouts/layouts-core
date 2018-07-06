<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Value;

final class Form extends Value
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $type;

    /**
     * Returns the form identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the form type.
     */
    public function getType(): string
    {
        return $this->type;
    }
}
