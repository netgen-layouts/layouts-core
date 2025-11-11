<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\Copy;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Copy::class)]
final class CopyTest extends JsonApiTestCase
{
    public function testCopy(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
                'description' => 'My new layout description',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/copy_layout',
            Response::HTTP_CREATED,
        );
    }

    public function testCopyInPublishedState(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
                'description' => 'My new layout description',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/7900306c-0351-5f0a-9b33-5d4f5a1f3943/copy?published=true&html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/copy_published_layout',
            Response::HTTP_CREATED,
        );
    }

    public function testCopyWithNonExistingDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/copy_layout_without_description',
            Response::HTTP_CREATED,
        );
    }

    public function testCopyWithEmptyDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
                'description' => '',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/copy_layout_empty_description',
            Response::HTTP_CREATED,
        );
    }

    public function testCopyWithNonExistingLayout(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/copy',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }

    public function testCopyWithInvalidName(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 42,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should be of type string.',
        );
    }

    public function testCopyWithMissingName(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should not be blank.',
        );
    }

    public function testCopyWithExistingName(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My other layout',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "layoutCopyStruct" has an invalid state. Layout with provided name already exists.',
        );
    }

    public function testCopyWithInvalidDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'New name',
                'description' => 42,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "description": This value should be of type string.',
        );
    }
}
