<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter\Text;

class TextTest extends BaseTest
{
    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter\Text
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
