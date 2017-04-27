<?php

namespace Netgen\BlockManager\Tests\Config\ConfigDefinition\Integration\Block;

use Netgen\BlockManager\Config\ConfigDefinition\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\Tests\Config\ConfigDefinition\Integration\BlockTest;

abstract class HttpCacheConfigTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface
     */
    public function createConfigDefinitionHandler()
    {
        return new HttpCacheConfigHandler($this->createMock(CacheableResolverInterface::class));
    }

    /**
     * @return array
     */
    public function configDataProvider()
    {
        return array(
            array(
                array(),
                array(
                    'use_http_cache' => false,
                    'shared_max_age' => null,
                ),
            ),
            array(
                array(
                    'use_http_cache' => false,
                ),
                array(
                    'use_http_cache' => false,
                    'shared_max_age' => null,
                ),
            ),
            array(
                array(
                    'use_http_cache' => false,
                    'shared_max_age' => 300,
                ),
                array(
                    'use_http_cache' => false,
                    'shared_max_age' => 300,
                ),
            ),
            array(
                array(
                    'use_http_cache' => false,
                    'shared_max_age' => 0,
                ),
                array(
                    'use_http_cache' => false,
                    'shared_max_age' => 0,
                ),
            ),
            array(
                array(
                    'use_http_cache' => true,
                ),
                array(
                    'use_http_cache' => true,
                    'shared_max_age' => null,
                ),
            ),
            array(
                array(
                    'use_http_cache' => true,
                    'shared_max_age' => 0,
                ),
                array(
                    'use_http_cache' => true,
                    'shared_max_age' => 0,
                ),
            ),
            array(
                array(
                    'use_http_cache' => true,
                    'shared_max_age' => 300,
                ),
                array(
                    'use_http_cache' => true,
                    'shared_max_age' => 300,
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
    public function invalidConfigDataProvider()
    {
        return array(
            array(
                array(
                    'use_http_cache' => 42,
                ),
            ),
            array(
                array(
                    'shared_max_age' => '42',
                ),
                array('use_http_cache', 'shared_max_age'),
            ),
            array(
                array(
                    'shared_max_age' => -5,
                ),
                array('use_http_cache', 'shared_max_age'),
            ),
        );
    }
}
