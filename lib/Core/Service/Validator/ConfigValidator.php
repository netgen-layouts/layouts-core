<?php

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Exception\Validation\ValidationException;
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
        $allowedConfigKeys = array_map(
            function (ConfigDefinitionInterface $configDefinition) {
                return $configDefinition->getConfigKey();
            },
            $configDefinitions
        );

        foreach ($configStructs as $configKey => $configStruct) {
            if (!in_array($configKey, $allowedConfigKeys, true)) {
                throw ValidationException::validationFailed(
                    'configStructs',
                    sprintf(
                        'Config definition with "%s" config key does not exist.',
                        $configKey
                    )
                );
            }
        }

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
