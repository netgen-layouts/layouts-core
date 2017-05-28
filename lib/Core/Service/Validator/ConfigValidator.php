<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct as ParameterStructConstraint;
use Symfony\Component\Validator\Constraints;

class ConfigValidator extends Validator
{
    /**
     * Validates config structs.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterCollectionInterface[] $configStructs
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateConfigStructs(array $configStructs, array $configDefinitions)
    {
        foreach ($configDefinitions as $configDefinition) {
            $configKey = $configDefinition->getConfigKey();
            if (!isset($configStructs[$configKey])) {
                continue;
            }

            $this->validate(
                $configStructs[$configKey],
                array(
                    new Constraints\Type(array('type' => ConfigStruct::class)),
                )
            );

            $this->validate(
                $configStructs[$configKey],
                array(
                    new ParameterStructConstraint(
                        array(
                            'parameterCollection' => $configDefinition,
                        )
                    ),
                ),
                'parameterValues'
            );
        }
    }
}
