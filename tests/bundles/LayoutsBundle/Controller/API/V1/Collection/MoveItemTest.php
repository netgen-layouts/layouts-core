<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Collection;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MoveItemTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection\MoveItem::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection\MoveItem::__invoke
     */
    public function testMoveItem(): void
    {
        $data = $this->jsonEncode(
            [
                'position' => 2,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection\MoveItem::__invoke
     */
    public function testMoveItemWithNonExistentItem(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/collections/items/ffffffff-ffff-ffff-ffff-ffffffffffff/move',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find item with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection\MoveItem::__invoke
     */
    public function testMoveItemWithOutOfRangePosition(): void
    {
        $data = $this->jsonEncode(
            [
                'position' => 9999,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "position" has an invalid state. Position is out of range.'
        );
    }
}
