<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink as ItemLinkConstraint;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate a link to an existing item in the system.
 */
final class ItemLinkType extends ParameterType
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    private $valueTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter
     */
    private $remoteIdConverter;

    public function __construct(ValueTypeRegistryInterface $valueTypeRegistry, RemoteIdConverter $remoteIdConverter)
    {
        $this->valueTypeRegistry = $valueTypeRegistry;
        $this->remoteIdConverter = $remoteIdConverter;
    }

    public function getIdentifier()
    {
        return 'item_link';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired(array('value_types', 'allow_invalid'));
        $optionsResolver->setAllowedTypes('value_types', 'array');
        $optionsResolver->setAllowedTypes('allow_invalid', 'bool');
        $optionsResolver->setDefault('value_types', array());
        $optionsResolver->setDefault('allow_invalid', false);

        $optionsResolver->setNormalizer(
            'value_types',
            function (Options $options, $value) {
                if (!empty($value)) {
                    return $value;
                }

                return array_keys(
                    $this->valueTypeRegistry->getValueTypes(true)
                );
            }
        );
    }

    public function export(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return $this->remoteIdConverter->convertToRemoteId($value);
    }

    public function import(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return $this->remoteIdConverter->convertFromRemoteId($value);
    }

    public function isValueEmpty(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        $parsedValue = parse_url($value);

        return empty($parsedValue['scheme']) || (empty($parsedValue['host'] && $parsedValue['host'] !== '0'));
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        $options = $parameterDefinition->getOptions();

        return array(
            new Constraints\Type(array('type' => 'string')),
            new ItemLinkConstraint(
                array(
                    'valueTypes' => $options['value_types'],
                    'allowInvalid' => $options['allow_invalid'],
                )
            ),
        );
    }
}
