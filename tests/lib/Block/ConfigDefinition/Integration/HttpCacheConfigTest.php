<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\ConfigDefinition\Integration;

use Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;

abstract class HttpCacheConfigTest extends BlockTest
{
    public function createConfigDefinitionHandler(): ConfigDefinitionHandlerInterface
    {
        return new HttpCacheConfigHandler();
    }

    public function configDataProvider(): array
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

    public function invalidConfigDataProvider(): array
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
