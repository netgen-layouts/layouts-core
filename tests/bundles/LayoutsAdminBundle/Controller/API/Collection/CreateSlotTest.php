<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\CreateSlot;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CreateSlot::class)]
final class CreateSlotTest extends JsonApiTestCase
{
    public function testCreateSlot(): void
    {
        $data = $this->jsonEncode(
            [
                'position' => 42,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/a79dde13-1f5c-51a6-bea9-b766236be49e/slots',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'collections/create_slot',
            Response::HTTP_CREATED,
        );
    }

    public function testCreateSlotWithNonExistentCollection(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/ffffffff-ffff-ffff-ffff-ffffffffffff/slots',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }

    public function testCreateSlotWithInvalidPosition(): void
    {
        $data = $this->jsonEncode(
            [
                'position' => '0',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/a79dde13-1f5c-51a6-bea9-b766236be49e/slots',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should be of type int.',
        );
    }

    public function testCreateSlotWithMissingPosition(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/a79dde13-1f5c-51a6-bea9-b766236be49e/slots',
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

    public function testCreateSlotWithNegativePosition(): void
    {
        $data = $this->jsonEncode(
            [
                'position' => -2,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/collections/a79dde13-1f5c-51a6-bea9-b766236be49e/slots',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should be either positive or zero.',
        );
    }
}
