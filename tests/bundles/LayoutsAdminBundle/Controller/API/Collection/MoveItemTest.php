<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\MoveItem;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(MoveItem::class)]
final class MoveItemTest extends JsonApiTestCase
{
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
