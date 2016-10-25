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
        $fields = array(
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
            'link' => array(
                new Constraints\IsNull(),
            ),
            'link_suffix' => array(
                new Constraints\Type(array('type' => 'string')),
            ),
            'new_window' => array(
                new Constraints\NotNull(),
                new Constraints\Type(array('type' => 'bool')),
            ),
        );

        if (isset($value['link_type'])) {
            if ($value['link_type'] === self::LINK_TYPE_URL) {
                $fields['link'] = array(
                    new Constraints\NotBlank(),
                    new Constraints\Url(),
                );
            } elseif ($value['link_type'] === self::LINK_TYPE_EMAIL) {
                $fields['link'] = array(
                    new Constraints\NotBlank(),
                    new Constraints\Email(),
                );
            } elseif ($value['link_type'] === self::LINK_TYPE_INTERNAL) {
                $fields['link'] = array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'scalar')),
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
