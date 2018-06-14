<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class ConditionType implements ConditionTypeInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $matches = true;

    public function __construct(string $type, bool $matches = true)
    {
        $this->type = $type;
        $this->matches = $matches;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getConstraints(): array
    {
        return [new Constraints\NotBlank()];
    }

    public function matches(Request $request, $value): bool
    {
        return $this->matches;
    }
}
