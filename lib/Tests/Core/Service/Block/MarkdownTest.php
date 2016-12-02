<?php

namespace Netgen\BlockManager\Tests\Core\Service\Block;

use Netgen\BlockManager\Block\BlockDefinition\Handler\MarkdownHandler;
use Michelf\Markdown;

abstract class MarkdownTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new MarkdownHandler(new Markdown());
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
                    'content' => '* Text',
                ),
                array(
                    'content' => '* Text',
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
