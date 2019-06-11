<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteItemTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\Collection\DeleteItem::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\Collection\DeleteItem::__invoke
     */
    public function testDeleteItem(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/api/v1/collections/items/89c214a3-204f-5352-85d7-8852b26ab6b0',
            [],
            [],
            [],
            $this->jsonEncode([])
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\Collection\DeleteItem::__invoke
     */
    public function testDeleteItemWithNonExistentItem(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/api/v1/collections/items/ffffffff-ffff-ffff-ffff-ffffffffffff',
            [],
            [],
            [],
            $this->jsonEncode([])
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find item with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }
}
