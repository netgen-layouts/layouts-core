<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Validator\Constraint\ItemLink;
use Symfony\Component\Validator\Constraints;

class Link extends Parameter
{
    const LINK_TYPE_URL = 'url';

    const LINK_TYPE_EMAIL = 'email';

    const LINK_TYPE_INTERNAL = 'internal';

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
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getValueConstraints($value)
    {
        $fields = array(
            'link_type' => array(
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    array(
                        'choices' => array(
                            self::LINK_TYPE_URL,
                            self::LINK_TYPE_EMAIL,
                            self::LINK_TYPE_INTERNAL,
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
            if ($value['link_type'] === self::LINK_TYPE_URL) {
                $fields['link'][] = new Constraints\Url();
            } elseif ($value['link_type'] === self::LINK_TYPE_EMAIL) {
                $fields['link'][] = new Constraints\Email();
            } elseif ($value['link_type'] === self::LINK_TYPE_INTERNAL) {
                $fields['link'][] = new ItemLink();
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
