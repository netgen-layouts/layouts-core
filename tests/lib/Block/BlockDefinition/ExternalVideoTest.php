<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\Handler\ExternalVideoHandler;

abstract class ExternalVideoTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new ExternalVideoHandler(array('youtube' => 'YouTube', 'vimeo' => 'Vimeo'));
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
                    'service' => 'youtube',
                ),
            ),
            array(
                array(
                    'service' => 'vimeo',
                ),
                array(
                    'service' => 'vimeo',
                ),
            ),
            array(
                array(
                ),
                array(
                    'video_id' => null,
                ),
            ),
            array(
                array(
                    'video_id' => null,
                ),
                array(
                    'video_id' => null,
                ),
            ),
            array(
                array(
                    'video_id' => '',
                ),
                array(
                    'video_id' => '',
                ),
            ),
            array(
                array(
                    'video_id' => '12345',
                ),
                array(
                    'video_id' => '12345',
                ),
            ),
            array(
                array(
                ),
                array(
                    'caption' => null,
                ),
            ),
            array(
                array(
                    'caption' => null,
                ),
                array(
                    'caption' => null,
                ),
            ),
            array(
                array(
                    'caption' => '',
                ),
                array(
                    'caption' => '',
                ),
            ),
            array(
                array(
                    'caption' => 'A caption',
                ),
                array(
                    'caption' => 'A caption',
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
                    'service' => 42,
                ),
            ),
            array(
                array(
                    'service' => 'dailymotion',
                ),
            ),
            array(
                array(
                    'video_id' => 42,
                ),
            ),
            array(
                array(
                    'caption' => 42,
                ),
            ),
        );
    }
}
