<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Utils\HydratorTrait;
use Stringable;

use function is_array;
use function is_object;
use function method_exists;

final class Parameter implements Stringable
{
    use HydratorTrait;

    /**
     * Returns the parameter name.
     */
    public private(set) string $name;

    /**
     * Returns the parameter definition.
     */
    public private(set) ParameterDefinition $parameterDefinition;

    /**
     * Returns the parameter value.
     */
    public private(set) mixed $value;

    /**
     * Returns if the parameter value is empty.
     */
    public private(set) bool $isEmpty;

    /**
     * Returns the value object if the parameter type supports it.
     */
    public private(set) ?object $valueObject {
        get {
            $parameterType = $this->parameterDefinition->type;
            if (!$parameterType instanceof ValueObjectProviderInterface) {
                throw ParameterException::valueObjectNotSupported($this->name, $parameterType::class);
            }

            return $this->valueObject ??= $parameterType->getValueObject($this->value);
        }
    }

    /**
     * Returns the string representation of the parameter value.
     */
    public function __toString(): string
    {
        if (is_array($this->value) || (is_object($this->value) && !method_exists($this->value, '__toString'))) {
            return '';
        }

        return (string) $this->value;
    }
}
