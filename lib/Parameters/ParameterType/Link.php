<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterDefinition\Link as LinkDefinition;
use Netgen\BlockManager\Validator\Constraint\ItemLink as ItemLinkConstraint;
use Symfony\Component\Validator\Constraints;

class Link extends ParameterType
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'link';
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getValueConstraints(ParameterDefinition $parameterDefinition, $value)
    {
        $options = $parameterDefinition->getOptions();

        $fields = array(
            'link_type' => array(
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    array(
                        'choices' => array(
                            LinkDefinition::LINK_TYPE_URL,
                            LinkDefinition::LINK_TYPE_EMAIL,
                            LinkDefinition::LINK_TYPE_PHONE,
                            LinkDefinition::LINK_TYPE_INTERNAL,
                        ),
                    )
                ),
            ),
            'link' => array(
                new Constraints\NotBlank(),
            ),
            'link_suffix' => array(
                new Constraints\Type(array('type' => 'string')),
            ),
            'new_window' => array(
                new Constraints\NotNull(),
                new Constraints\Type(array('type' => 'bool')),
            ),
        );

        if (is_array($value) && isset($value['link_type'])) {
            if ($value['link_type'] === LinkDefinition::LINK_TYPE_URL) {
                $fields['link'][] = new Constraints\Url();
            } elseif ($value['link_type'] === LinkDefinition::LINK_TYPE_EMAIL) {
                $fields['link'][] = new Constraints\Email();
            } elseif ($value['link_type'] === LinkDefinition::LINK_TYPE_PHONE) {
                $fields['link'][] = new Constraints\Type(array('type' => 'string'));
            } elseif ($value['link_type'] === LinkDefinition::LINK_TYPE_INTERNAL) {
                $fields['link'][] = new ItemLinkConstraint(
                    array(
                        'valueTypes' => $options['value_types'],
                    )
                );
            }
        }

        return array(
            new Constraints\Collection(
                array(
                    'fields' => $fields,
                )
            ),
        );
    }
}
