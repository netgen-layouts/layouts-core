<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Collection;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class DeleteItemsTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Collection\DeleteItems::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Collection\DeleteItems::__invoke
     */
    public function testDeleteItems()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/collections/1/items',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Collection\DeleteItems::__invoke
     */
    public function testDeleteItemsWithNonExistentCollection()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/collections/9999/items',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find collection with identifier "9999"'
        );
    }
}
