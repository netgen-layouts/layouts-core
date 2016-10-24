<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;

class Uri extends Parameter
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
        return 'uri';
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
        if (!is_array($value)) {
            return array();
        }

        $fields = array();

        if (isset($value['link_type'])) {
            if ($value['link_type'] === self::LINK_TYPE_URL) {
                $fields[$value['link_type']] = array(
                    new Constraints\NotBlank(),
                    new Constraints\Url(),
                );
            } elseif ($value['link_type'] === self::LINK_TYPE_EMAIL) {
                $fields[$value['link_type']] = array(
                    new Constraints\NotBlank(),
                    new Constraints\Url(),
                );
            } elseif ($value['link_type'] === self::LINK_TYPE_INTERNAL) {
                $fields[$value['link_type']] = array(
                    new Constraints\NotBlank(),
                );

                $fields['internal_link_suffix'] = array();
            }
        }

        return array(
            new Constraints\Collection(
                array(
                    'fields' => array(
                        'link_type' => array(
                            new Constraints\NotBlank(),
                            new Constraints\Choice(
                                array(
                                    'choices' => array(
                                        self::LINK_TYPE_URL,
                                        self::LINK_TYPE_EMAIL,
                                        self::LINK_TYPE_INTERNAL
                                    )
                                )
                            )
                        ),
                        'open_in_new_window' => array(
                            new Constraints\NotNull(),
                        ),
                    ) + $fields,
                )
            ),
        );
    }
}
