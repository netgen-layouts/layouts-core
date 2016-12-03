<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\Handler\FullViewHandler;

abstract class FullViewTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new FullViewHandler('content');
    }

    /**
     * @return array
     */
    public function parametersDataProvider()
    {
        return array(
            array(
                array(),
                array(),
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
                array(),
            ),
        );
    }
}
