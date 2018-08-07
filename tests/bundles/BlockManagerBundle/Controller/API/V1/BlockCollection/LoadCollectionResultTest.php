<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\BlockCollection;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadCollectionResultTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection\LoadCollectionResult::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection\LoadCollectionResult::__invoke
     */
    public function testLoadCollectionResult(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/en/blocks/35/collections/default/result');

        self::assertResponse(
            $this->client->getResponse(),
            'v1/block_collections/load_collection_result',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection\LoadCollectionResult::__invoke
     */
    public function testLoadCollectionResultWithNonExistentBlock(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/en/blocks/9999/collections/default/result');

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockCollection\LoadCollectionResult::__invoke
     */
    public function testLoadCollectionResultWithNonExistentCollection(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/en/blocks/31/collections/unknown/result');

        self::assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'Collection with "unknown" identifier does not exist in the block.'
        );
    }
}
