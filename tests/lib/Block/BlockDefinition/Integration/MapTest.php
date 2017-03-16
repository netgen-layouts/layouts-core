<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Integration;

use Netgen\BlockManager\Block\BlockDefinition\Handler\MapHandler;

abstract class MapTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new MapHandler(3, 7, array('ROADMAP' => 'Roadmap', 'TERRAIN' => 'Terrain'));
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
                    'latitude' => 0,
                ),
            ),
            array(
                array(
                    'latitude' => 42,
                ),
                array(
                    'latitude' => 42,
                ),
            ),
            array(
                array(),
                array(
                    'longitude' => 0,
                ),
            ),
            array(
                array(
                    'longitude' => 42,
                ),
                array(
                    'longitude' => 42,
                ),
            ),
            array(
                array(),
                array(
                    'zoom' => 5,
                ),
            ),
            array(
                array(
                    'zoom' => 6,
                ),
                array(
                    'zoom' => 6,
                ),
            ),
            array(
                array(),
                array(
                    'map_type' => 'ROADMAP',
                ),
            ),
            array(
                array(
                    'map_type' => 'TERRAIN',
                ),
                array(
                    'map_type' => 'TERRAIN',
                ),
            ),
            array(
                array(),
                array(
                    'show_marker' => true,
                ),
            ),
            array(
                array(
                    'show_marker' => null,
                ),
                array(
                    'show_marker' => null,
                ),
            ),
            array(
                array(
                    'show_marker' => false,
                ),
                array(
                    'show_marker' => false,
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
                    'latitude' => null,
                ),
            ),
            array(
                array(
                    'latitude' => -100,
                ),
            ),
            array(
                array(
                    'latitude' => 'lat',
                ),
            ),
            array(
                array(
                    'longitude' => null,
                ),
            ),
            array(
                array(
                    'longitude' => -200,
                ),
            ),
            array(
                array(
                    'longitude' => 'long',
                ),
            ),
            array(
                array(
                    'zoom' => null,
                ),
            ),
            array(
                array(
                    'zoom' => 10,
                ),
            ),
            array(
                array(
                    'zoom' => 'zoom',
                ),
            ),
            array(
                array(
                    'map_type' => null,
                ),
            ),
            array(
                array(
                    'map_type' => 'unknown',
                ),
            ),
            array(
                array(
                    'map_type' => 42,
                ),
            ),
            array(
                array(
                    'show_marker' => 42,
                ),
            ),
        );
    }
}
