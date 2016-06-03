<?php

namespace Netgen\BlockManager\Tests\Configuration\Factory;

use Netgen\BlockManager\Configuration\Factory\SourceFactory;
use Netgen\BlockManager\Configuration\Source\Query;
use Netgen\BlockManager\Configuration\Source\Source;

class SourceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Configuration\Factory\SourceFactory::buildSource
     */
    public function testBuildSource()
    {
        $source = SourceFactory::buildSource(
            'dynamic',
            array(
                'name' => 'Dynamic source',
                'enabled' => true,
                'queries' => array(
                    'default' => array(
                        'query_type' => 'ezcontent_search',
                        'default_parameters' => array(
                            'parent_location_id' => 2,
                            'content_types' => array('news', 'article'),
                        ),
                    ),
                ),
            )
        );

        self::assertEquals(
            new Source(
                'dynamic',
                true,
                'Dynamic source',
                array(
                    'default' => new Query(
                        'default',
                        'ezcontent_search',
                        array(
                            'parent_location_id' => 2,
                            'content_types' => array('news', 'article'),
                        )
                    ),
                )
            ),
            $source
        );
    }
}
