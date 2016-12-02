<?php

namespace Netgen\BlockManager\Tests\Core\Service\Block;

use Netgen\BlockManager\Block\BlockDefinition\Handler\HtmlSnippetHandler;

abstract class HtmlSnippetTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new HtmlSnippetHandler();
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
                    'content' => null,
                ),
            ),
            array(
                array(
                    'content' => null,
                ),
                array(
                    'content' => null,
                ),
            ),
            array(
                array(
                    'content' => '',
                ),
                array(
                    'content' => '',
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
                    'content' => 42,
                ),
            ),
        );
    }
}
