<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CopyTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopy(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
                'description' => 'My new layout description',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/copy?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/copy_layout',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyInPublishedState(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
                'description' => 'My new layout description',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/6/copy?published=true&html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/copy_published_layout',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithNonExistingDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/copy?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/copy_layout_without_description',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithEmptyDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
                'description' => '',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/copy?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/copy_layout_empty_description',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithNonExistingLayout(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout name',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/9999/copy',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithInvalidName(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 42,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/copy',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithMissingName(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/copy',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithExistingName(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My other layout',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/copy',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "layoutCopyStruct" has an invalid state. Layout with provided name already exists.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Copy::__invoke
     */
    public function testCopyWithInvalidDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'New name',
                'description' => 42,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/copy',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "description": This value should be of type string.'
        );
    }
}
