<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler;

abstract class TwigBlockTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new TwigBlockHandler();
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
                    'block_name' => null,
                ),
            ),
            array(
                array(
                    'block_name' => null,
                ),
                array(
                    'block_name' => null,
                ),
            ),
            array(
                array(
                    'block_name' => '',
                ),
                array(
                    'block_name' => '',
                ),
            ),
            array(
                array(
                    'block_name' => 'block',
                ),
                array(
                    'block_name' => 'block',
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
                    'block_name' => 42,
                ),
            ),
        );
    }
}
