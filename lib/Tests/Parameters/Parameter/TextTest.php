<?php

namespace Netgen\BlockManager\Tests\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter\Text;

class TextTest extends BaseTest
{
    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\Parameter\Text
     */
    public function getParameter($options)
    {
        return new Text('Test value', false, $options);
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
