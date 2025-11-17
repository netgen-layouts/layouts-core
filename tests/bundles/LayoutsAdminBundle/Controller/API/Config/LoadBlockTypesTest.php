<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Config;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config\LoadBlockTypes;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadBlockTypes::class)]
final class LoadBlockTypesTest extends ApiTestCase
{
    public function testLoadBlockTypes(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/config/block_types')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('type(block_types)', 'array')
            ->assertJsonMatches('length(block_types) > `0`', true)
            ->assertJsonMatches('type(block_type_groups)', 'array')
            ->assertJsonMatches('length(block_type_groups) > `0`', true);
    }
}
