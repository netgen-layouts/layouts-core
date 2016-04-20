<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter\Select;

class SelectTest extends BaseTest
{
    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter\Select
     */
    public function getParameter($options)
    {
        return new Select('Test value', false, $options);
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validOptionsProvider()
    {
        return array(
            array(
                array(
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
                array(
                    'multiple' => false,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'multiple' => false,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
                array(
                    'multiple' => false,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'multiple' => true,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
                array(
                    'multiple' => true,
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
        );
    }

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return array
     */
    public function invalidOptionsProvider()
    {
        return array(
            array(
                array(
                    'multiple' => 'true',
                    'options' => array(
                        'Option 1' => 'o1',
                        'Option 2' => 'o2',
                    ),
                ),
            ),
            array(
                array(
                    'options' => 'options',
                ),
            ),
            array(
                array(
                    'options' => array(),
                ),
            ),
            array(
                array(
                    'undefined_value' => 'Value',
                ),
            ),
            array(
                array(),
            ),
        );
    }
}
