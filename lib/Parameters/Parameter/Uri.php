<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;

class Uri extends Parameter
{
    const LINK_TYPE_NONE = 'none';

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

        $fields = array(
            self::LINK_TYPE_URL => null,
            self::LINK_TYPE_EMAIL => null,
            self::LINK_TYPE_INTERNAL => null,
            'internal_suffix' => null,
        );

        if (isset($value['link_type'])) {
            if ($value['link_type'] === self::LINK_TYPE_URL) {
                $fields[self::LINK_TYPE_URL] = array(
                    new Constraints\NotBlank(),
                    new Constraints\Url(),
                );
            } elseif ($value['link_type'] === self::LINK_TYPE_EMAIL) {
                $fields[self::LINK_TYPE_EMAIL] = array(
                    new Constraints\NotBlank(),
                    new Constraints\Email(),
                );
            } elseif ($value['link_type'] === self::LINK_TYPE_INTERNAL) {
                $fields[self::LINK_TYPE_INTERNAL] = array(
                    new Constraints\NotBlank(),
                );

                $fields['internal_suffix'] = array(
                    new Constraints\Type(array('type' => 'string')),
                );
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
                                    'callback' => function () {
                                        $linkTypes = array(
                                            self::LINK_TYPE_URL,
                                            self::LINK_TYPE_EMAIL,
                                            self::LINK_TYPE_INTERNAL,
                                        );

                                        if (!$this->isRequired()) {
                                            array_unshift($linkTypes, self::LINK_TYPE_NONE);
                                        }

                                        return $linkTypes;
                                    },
                                )
                            ),
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
