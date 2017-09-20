<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink as ItemLinkConstraint;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate a link to an existing item in the system.
 */
class ItemLinkType extends ParameterType
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    private $valueTypeRegistry;

    public function __construct(ValueTypeRegistryInterface $valueTypeRegistry)
    {
        $this->valueTypeRegistry = $valueTypeRegistry;
    }

    public function getIdentifier()
    {
        return 'item_link';
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired(array('value_types'));
        $optionsResolver->setAllowedTypes('value_types', 'array');
        $optionsResolver->setDefault('value_types', array());

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

    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        $parsedValue = parse_url($value);

        return empty($parsedValue['scheme']) || empty($parsedValue['host']);
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        return array(
            new Constraints\Type(array('type' => 'string')),
            new ItemLinkConstraint(
                array(
                    'valueTypes' => $parameter->getOption('value_types'),
                )
            ),
        );
    }
}
