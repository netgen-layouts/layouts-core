<?php

namespace Netgen\BlockManager\Validator\Structs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct as ConfigAwareStructConstraint;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates the complete value which implements ConfigAwareStruct interface.
 */
final class ConfigAwareStructValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConfigAwareStructConstraint) {
            throw new UnexpectedTypeException($constraint, ConfigAwareStructConstraint::class);
        }

        if (!$constraint->payload instanceof ConfigAwareValue) {
            throw new UnexpectedTypeException($constraint->payload, ConfigAwareValue::class);
        }

        if (!$value instanceof ConfigAwareStruct) {
            throw new UnexpectedTypeException($value, ConfigAwareStruct::class);
        }

        /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface $validator */
        $validator = $this->context->getValidator()->inContext($this->context);
        $configs = $constraint->payload->getConfigs();

        foreach ($value->getConfigStructs() as $configKey => $configStruct) {
            if (!isset($configs[$configKey])) {
                continue;
            }

            $validator->atPath('configStructs[' . $configKey . '].parameterValues')->validate(
                $configStruct,
                [
                    new ParameterStruct(
                        [
                            'parameterCollection' => $configs[$configKey]->getDefinition(),
                            'allowMissingFields' => true,
                        ]
                    ),
                ]
            );
        }
    }
}
