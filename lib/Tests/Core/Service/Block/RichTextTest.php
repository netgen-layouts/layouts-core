<?php

namespace Netgen\BlockManager\Tests\Core\Service\Block;

use Netgen\BlockManager\Block\BlockDefinition\Handler\RichTextHandler;

abstract class RichTextTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new RichTextHandler();
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
                    'content' => '<b>Text</b>',
                ),
                array(
                    'content' => '<b>Text</b>',
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
