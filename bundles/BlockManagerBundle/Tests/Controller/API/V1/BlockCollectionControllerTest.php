<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class BlockCollectionControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollectionController::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollectionController::loadCollectionReference
     */
    public function testLoadCollectionReference()
    {
        $this->client->request('GET', '/bm/api/v1/blocks/1/collections/default');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/block_collections/load_collection_reference',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollectionController::loadCollectionReferences
     */
    public function testLoadCollectionReferences()
    {
        $this->client->request('GET', '/bm/api/v1/blocks/1/collections');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/block_collections/load_collection_references',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollectionController::loadCollectionReferences
     */
    public function testLoadCollectionReferencesWithNonExistentBlock()
    {
        $this->client->request('GET', '/bm/api/v1/blocks/9999/collections');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollectionController::loadCollectionResult
     */
    public function testLoadCollectionResult()
    {
        $this->client->request('GET', '/bm/api/v1/blocks/5/collections/default/result');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/block_collections/load_collection_result',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollectionController::loadCollectionResult
     */
    public function testLoadCollectionResultWithNonExistentBlock()
    {
        $this->client->request('GET', '/bm/api/v1/blocks/9999/collections/default/result');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollectionController::loadCollectionResult
     */
    public function testLoadCollectionResultWithNonExistentCollectionReference()
    {
        $this->client->request('GET', '/bm/api/v1/blocks/1/collections/unknown/result');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }
}
