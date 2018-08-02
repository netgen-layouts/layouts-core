<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class TargetType2 implements TargetTypeInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public static function getType(): string
    {
        return 'target2';
    }

    public function getConstraints(): array
    {
        return [new Constraints\NotBlank()];
    }

    public function provideValue(Request $request)
    {
        return $this->value;
    }
}
