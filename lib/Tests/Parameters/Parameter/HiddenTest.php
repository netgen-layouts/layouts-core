<?php

namespace Netgen\BlockManager\Tests\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter\Hidden;

class HiddenTest extends BaseTest
{
    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\Parameter\Hidden
     */
    public function getParameter($options)
    {
        return new Hidden('Test value', false, $options);
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
                array(),
                array(),
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
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }
}
