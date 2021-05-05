<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MoveItemTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\MoveItem::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\MoveItem::__invoke
     */
    public function testMoveItem(): void
    {
        $data = $this->jsonEncode(
            [
                'position' => 2,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\MoveItem::__invoke
     */
    public function testMoveItemWithNonExistentItem(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/items/ffffffff-ffff-ffff-ffff-ffffffffffff/move',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find item with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\MoveItem::__invoke
     */
    public function testMoveItemWithInvalidPosition(): void
    {
        $data = $this->jsonEncode(
            [
                'position' => '0',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            '/^There was an error validating "position": This value should be of type int(eger)?.$/',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\MoveItem::__invoke
     */
    public function testMoveItemWithMissingPosition(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should not be blank.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\MoveItem::__invoke
     */
    public function testMoveItemWithNegativePosition(): void
    {
        $data = $this->jsonEncode(
            [
                'position' => -2,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should be greater than or equal to 0.',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\MoveItem::__invoke
     */
    public function testMoveItemWithOutOfRangePosition(): void
    {
        $data = $this->jsonEncode(
            [
                'position' => 9999,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "position" has an invalid state. Position is out of range.',
        );
    }
}
