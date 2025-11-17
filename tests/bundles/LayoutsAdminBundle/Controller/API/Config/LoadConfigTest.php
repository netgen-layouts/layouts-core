<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Config;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config\LoadConfig;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadConfig::class)]
final class LoadConfigTest extends ApiTestCase
{
    public function testLoadConfig(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/config')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('type(@)', 'object')
            ->assertJsonMatches('type(csrf_token)', 'string')
            ->assertJsonMatches('length(csrf_token) > `0`', true);
    }
}
