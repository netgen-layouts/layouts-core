<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Config;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config\LoadLayoutTypes;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadLayoutTypes::class)]
final class LoadLayoutTypesTest extends ApiTestCase
{
    public function testLoadLayoutTypes(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/config/layout_types')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('type(@)', 'array')
            ->assertJsonMatches('length(@) > `0`', true);
    }
}
