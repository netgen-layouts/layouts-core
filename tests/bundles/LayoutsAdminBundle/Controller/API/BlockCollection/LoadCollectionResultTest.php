<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\BlockCollection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\LoadCollectionResult;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadCollectionResult::class)]
final class LoadCollectionResultTest extends ApiTestCase
{
    public function testLoadCollectionResult(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/blocks/c2a30ea3-95ef-55b0-a584-fbcfd93cec9e/collections/default/result')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('block_collections/load_collection_result');
    }

    public function testLoadCollectionResultWithNonExistentBlock(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/collections/default/result')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testLoadCollectionResultWithNonExistentCollection(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/unknown/result')
            ->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'Collection with "unknown" identifier does not exist in the block.');
    }
}
