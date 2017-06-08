<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink as ItemLinkConstraint;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ItemLinkType extends ParameterType
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    protected $valueTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface $valueTypeRegistry
     */
    public function __construct(ValueTypeRegistryInterface $valueTypeRegistry)
    {
        $this->valueTypeRegistry = $valueTypeRegistry;
    }

    /**
     * Returns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'item_link';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
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

    /**
     * Returns if the parameter value is empty.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty($value)
    {
        $parsedValue = parse_url($value);

        return empty($parsedValue['scheme']) || empty($parsedValue['host']);
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
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
