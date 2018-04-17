<?php

namespace Netgen\BlockManager\Tests\Block\ConfigDefinition\Integration\Block;

use Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\Tests\Block\ConfigDefinition\Integration\BlockTest;

abstract class HttpCacheConfigTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
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
        return [
            [
                [],
                [
                    'use_http_cache' => false,
                    'shared_max_age' => null,
                ],
            ],
            [
                [
                    'use_http_cache' => false,
                ],
                [
                    'use_http_cache' => false,
                    'shared_max_age' => null,
                ],
            ],
            [
                [
                    'use_http_cache' => false,
                    'shared_max_age' => 300,
                ],
                [
                    'use_http_cache' => false,
                    'shared_max_age' => 300,
                ],
            ],
            [
                [
                    'use_http_cache' => false,
                    'shared_max_age' => 0,
                ],
                [
                    'use_http_cache' => false,
                    'shared_max_age' => 0,
                ],
            ],
            [
                [
                    'use_http_cache' => true,
                ],
                [
                    'use_http_cache' => true,
                    'shared_max_age' => null,
                ],
            ],
            [
                [
                    'use_http_cache' => true,
                    'shared_max_age' => 0,
                ],
                [
                    'use_http_cache' => true,
                    'shared_max_age' => 0,
                ],
            ],
            [
                [
                    'use_http_cache' => true,
                    'shared_max_age' => 300,
                ],
                [
                    'use_http_cache' => true,
                    'shared_max_age' => 300,
                ],
            ],
            [
                [
                    'unknown' => 'unknown',
                ],
                [],
            ],
        ];
    }

    /**
     * @return array
     */
    public function invalidConfigDataProvider()
    {
        return [
            [
                [
                    'use_http_cache' => 42,
                ],
            ],
            [
                [
                    'shared_max_age' => '42',
                ],
                ['use_http_cache', 'shared_max_age'],
            ],
            [
                [
                    'shared_max_age' => -5,
                ],
                ['use_http_cache', 'shared_max_age'],
            ],
        ];
    }
}
