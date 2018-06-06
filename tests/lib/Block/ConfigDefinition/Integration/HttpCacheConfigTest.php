<?php

namespace Netgen\BlockManager\Tests\Block\ConfigDefinition\Integration;

use Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler;

abstract class HttpCacheConfigTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    public function createConfigDefinitionHandler()
    {
        return new HttpCacheConfigHandler();
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
            ],
            [
                [
                    'shared_max_age' => -5,
                ],
            ],
        ];
    }
}
