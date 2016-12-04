<?php

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;

return array(
    new Location(
        array(
            'id' => 140,
            'contentInfo' => new ContentInfo(
                array(
                    'name' => 'Will Starbucks Lose Its Coffee Smell To Fresh Croissants?',
                )
            ),
        )
    ),
    new Location(
        array(
            'id' => 79,
            'contentInfo' => new ContentInfo(
                array(
                    'name' => 'Airbus Regains Top Spot From Boeing In Q1',
                )
            ),
        )
    ),
    new Location(
        array(
            'id' => 78,
            'contentInfo' => new ContentInfo(
                array(
                    'name' => 'British Airways rolls out first painted A380',
                )
            ),
        )
    ),
);
