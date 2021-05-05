<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\BlockCollection;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadCollectionResultTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\LoadCollectionResult::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\LoadCollectionResult::__invoke
     */
    public function testLoadCollectionResult(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/blocks/c2a30ea3-95ef-55b0-a584-fbcfd93cec9e/collections/default/result');

        $this->assertResponse(
            $this->client->getResponse(),
            'block_collections/load_collection_result',
            Response::HTTP_OK,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\LoadCollectionResult::__invoke
     */
    public function testLoadCollectionResultWithNonExistentBlock(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/collections/default/result');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\LoadCollectionResult::__invoke
     */
    public function testLoadCollectionResultWithNonExistentCollection(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/unknown/result');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'Collection with "unknown" identifier does not exist in the block.',
        );
    }
}
