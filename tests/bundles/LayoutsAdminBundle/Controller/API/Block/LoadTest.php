<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Load;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Load::class)]
final class LoadTest extends ApiTestCase
{
    public function testLoad(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de?html=false')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('blocks/view_block');
    }

    public function testLoadInPublishedState(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de?published=true&html=false')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('blocks/view_published_block');
    }

    public function testLoadWithNonExistentBlock(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
