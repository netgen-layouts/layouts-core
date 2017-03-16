<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Integration;

use Netgen\BlockManager\Block\BlockDefinition\Handler\TextHandler;

abstract class TextTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new TextHandler();
    }

    /**
     * @return array
     */
    public function parametersDataProvider()
    {
        return array(
            array(
                array(),
                array(
                    'content' => 'Text',
                ),
            ),
            array(
                array(
                    'content' => 'New Text',
                ),
                array(
                    'content' => 'New Text',
                ),
            ),
            array(
                array(
                    'unknown' => 'unknown',
                ),
                array(),
            ),
        );
    }

    /**
     * @return array
     */
    public function invalidParametersDataProvider()
    {
        return array(
            array(
                array(
                    'content' => null,
                ),
            ),
            array(
                array(
                    'content' => '',
                ),
            ),
            array(
                array(
                    'content' => 42,
                ),
            ),
        );
    }
}
